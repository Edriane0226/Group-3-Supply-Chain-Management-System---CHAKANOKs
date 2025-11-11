<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Email\Email;

class Contact extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Contact Us - Chakanoks',
            'validation' => \Config\Services::validation()
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
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'min_length' => 'The {field} must be at least {param} characters long.',
                    'max_length' => 'The {field} cannot exceed {param} characters.'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'valid_email' => 'Please provide a valid {field} address.'
                ]
            ],
            'subject' => [
                'label' => 'Subject',
                'rules' => 'required|min_length[5]|max_length[100]',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'min_length' => 'The {field} must be at least {param} characters long.'
                ]
            ],
            'message' => [
                'label' => 'Message',
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'min_length' => 'The {field} must be at least {param} characters long.'
                ]
            ]
        ];

        // Run validation
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Get form data
        $name = esc($this->request->getPost('name'));
        $email = esc($this->request->getPost('email'));
        $subject = esc($this->request->getPost('subject'));
        $message = esc($this->request->getPost('message'));

        try {
            $emailService = \Config\Services::email();
            
            // Build the email content
            $emailContent = view('emails/contact_form', [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => nl2br(htmlspecialchars($message))
            ]);
            
            // Set email parameters
            $emailService->setTo(config('Email')->recipients);
            $emailService->setFrom($email, $name);
            $emailService->setReplyTo($email, $name);
            $emailService->setSubject("Contact Form: $subject");
            $emailService->setMessage($emailContent);

            // Send email
            if ($emailService->send()) {
                return redirect()->to('/contact')
                    ->with('success', 'Thank you for contacting us! We will get back to you soon.');
            } else {
                log_message('error', 'Email sending failed: ' . $emailService->printDebugger(['headers']));
                return redirect()->back()
                    ->with('error', 'Failed to send your message. Please try again later.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', 'Contact form error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while sending your message. Please try again.')
                ->withInput();
        }
    }
}
