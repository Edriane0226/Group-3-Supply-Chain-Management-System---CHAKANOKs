<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Email\Email;
use App\Models\ContactMessageModel;

class Contact extends BaseController
{
    private const ALPHANUMERIC_SPACE_NEWLINE_RULE = 'regex_match[/^[A-Za-z0-9 \r\n]+$/]';
    protected ContactMessageModel $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactMessageModel();
    }
    public function index()
    {
        // Get flashdata for debugging
        $success = session()->getFlashdata('success');
        $error = session()->getFlashdata('error');
        $errors = session()->getFlashdata('errors');
        
        $data = [
            'title' => 'Contact Us - Chakanoks',
            'validation' => \Config\Services::validation(),
            'success' => $success,
            'error' => $error,
            'errors' => $errors
        ];
        return view('auth/contact', $data);
    }

    public function send()
    {
        // Load the validation service
        $validation = \Config\Services::validation();
        
        // Set validation rules
        $rules = [
            'name' => [
                'label' => 'Name',
                'rules' => 'required|' . self::ALPHANUMERIC_SPACE_RULE . '|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'min_length' => 'The {field} must be at least {param} characters long.',
                    'max_length' => 'The {field} cannot exceed {param} characters.',
                    'regex_match' => 'The {field} may only contain letters, numbers, spaces, and line breaks.'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_unique[contact_messages.email]',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'valid_email' => 'Please provide a valid {field} address.',
                    'is_unique' => 'We have already received a message from this {field}. Please use a different email.'
                ]
            ],
            'subject' => [
                'label' => 'Subject',
                'rules' => 'required|' . self::ALPHANUMERIC_SPACE_RULE . '|min_length[5]|max_length[100]',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'min_length' => 'The {field} must be at least {param} characters long.',
                    'regex_match' => 'The {field} may only contain letters, numbers, spaces, and line breaks.'
                ]
            ],
            'message' => [
                'label' => 'Message',
                'rules' => 'required|' . self::ALPHANUMERIC_SPACE_NEWLINE_RULE . '|min_length[10]',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'min_length' => 'The {field} must be at least {param} characters long.',
                    'regex_match' => 'The {field} may only contain letters, numbers, spaces, and line breaks.'
                ]
            ]
        ];

        // Run validation
        if (!$this->validate($rules)) {
            $validationErrors = $validation->getErrors();
            log_message('debug', 'Contact form validation failed: ' . json_encode($validationErrors));
            return redirect()->back()
                ->withInput()
                ->with('errors', $validationErrors)
                ->with('error', 'Please fix the validation errors below.');
        }

        // Get form data
        $name = esc($this->request->getPost('name'));
        $email = esc($this->request->getPost('email'));
        $subject = esc($this->request->getPost('subject'));
        $message = esc($this->request->getPost('message'));

        try {
            // Save to database
            $messageData = [
                'name'       => $name,
                'email'      => $email,
                'subject'    => $subject,
                'message'    => $message,
                'status'     => 'unread',
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
            ];

            // Try to insert into database
            $insertResult = $this->contactModel->insert($messageData);
            
            if (!$insertResult) {
                $errors = $this->contactModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                
                log_message('error', 'Failed to save contact message: ' . $errorMessage);
                log_message('error', 'Contact message data: ' . json_encode($messageData));
                
                // Check if it's a database connection issue
                $db = \Config\Database::connect();
                if (!$db->connID) {
                    return redirect()->back()
                        ->with('error', 'Database connection error. Please try again later.')
                        ->withInput();
                }
                
                // Return error to user
                return redirect()->back()
                    ->with('error', 'Failed to save your message. Please try again. Error: ' . $errorMessage)
                    ->withInput();
            }
            
            // Log successful database save
            log_message('info', 'Contact message saved successfully. ID: ' . $this->contactModel->getInsertID());

            // Try to send email using EmailService (with SMTP configuration)
            // Note: Email sending failure won't prevent success message since message is already saved
            $emailSent = false;
            try {
                $emailService = new \App\Libraries\EmailService();
                
                // Get recipient email from config
                $recipientEmail = config('Email')->recipients ?? config('Email')->fromEmail ?? 'marcobatiller07@gmail.com';
                
                // Build the email content
                $emailContent = view('emails/contact_form', [
                    'name' => $name,
                    'email' => $email,
                    'subject' => $subject,
                    'message' => nl2br(htmlspecialchars($message))
                ]);
                
                // Send email using EmailService (which handles SMTP configuration)
                // Set Reply-To to user's email so admin can reply directly
                $emailSent = $emailService->send(
                    $recipientEmail,
                    "Contact Form: $subject",
                    $emailContent,
                    null, // no attachment
                    null, // no attachment name
                    $email, // reply-to: user's email
                    $name  // reply-to name: user's name
                );
                
                if ($emailSent) {
                    log_message('info', 'Contact form email sent successfully to: ' . $recipientEmail);
                } else {
                    log_message('warning', 'Contact form email sending failed. Check SMTP configuration.');
                }
            } catch (\Exception $emailError) {
                // Log but don't fail the request since message is already saved
                log_message('error', 'Contact form email error: ' . $emailError->getMessage());
                log_message('error', 'Stack trace: ' . $emailError->getTraceAsString());
            }

            // Show success message (message is saved even if email failed)
            $successMessage = 'Your message has been sent successfully! We will get back to you soon.';
            if (!$emailSent) {
                $successMessage .= ' (Note: Email notification may not have been sent, but your message was saved.)';
            }
            
            log_message('info', 'Contact form success - redirecting with message: ' . $successMessage);
            
            return redirect()->to('/contact')
                ->with('success', $successMessage);
                
        } catch (\Exception $e) {
            log_message('error', 'Contact form error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while sending your message. Please try again.')
                ->withInput();
        }
    }
}
