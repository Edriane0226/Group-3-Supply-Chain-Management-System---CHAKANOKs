<?php

namespace App\Controllers;

use App\Models\BranchModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class BranchManagement extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
        helper(['form', 'url']);
    }

    private function authorize()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Please login first.');
        }

        if ($this->session->get('role') !== 'Central Office Admin') {
            return redirect()->to(site_url('login'))
                ->with('error', 'Unauthorized access to Branch Management.');
        }

        return null; // Authorized
    }

    //Pang access ni siya sa branchManagement page at the same time gikuha niya ang branches store to $data['branches']
    public function index()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $branchModel = new BranchModel();
        $data['branches'] = $branchModel->findAll();

        return view('pages/branchManagement', $data);
    }

     //Paadto sa createBranch form
    public function create()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        return view('branchManager/createBranch');
    }

    /**
     * Save new branch to database
     */
    public function store()
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $rules = [
            'branch_name'  => 'required|min_length[3]|max_length[150]|regex_match[/^[A-Za-z0-9\s]+$/]',
            'location'     => 'required|min_length[3]|max_length[255]|regex_match[/^[A-Za-z0-9\s]+$/]',
            'contact_info' => 'permit_empty|regex_match[/^[0-9]{7,15}$/]',
            'status'       => 'required|in_list[existing,upcoming,franchise]',
        ];

        $messages = [
            'branch_name' => [
                'required'   => 'Branch name is required.',
                'min_length' => 'Branch name must be at least 3 characters.',
                'max_length' => 'Branch name may not exceed 150 characters.',
                'regex_match' => 'Branch name may only contain letters, numbers, and spaces.',
            ],
            'location' => [
                'required'   => 'Location is required.',
                'min_length' => 'Location must be at least 3 characters.',
                'max_length' => 'Location may not exceed 255 characters.',
                'regex_match' => 'Location may only contain letters, numbers, and spaces.',
            ],
            'contact_info' => [
                'regex_match' => 'Contact number must be 7 to 15 digits.',
            ],
            'status' => [
                'required' => 'Please select a status for the branch.',
                'in_list'  => 'Invalid status selected. Choose from existing, upcoming, or franchise.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            $message = 'Validation failed. Please review the highlighted fields.';

            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $message,
                        'errors'  => $errors,
                    ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $errors)
                ->with('error', $message);
        }

        $branchModel = new BranchModel();

        //Gikuha niya data from the createBranch form POST 
        $data = [
            'branch_name'  => trim((string) $this->request->getPost('branch_name')),
            'location'     => trim((string) $this->request->getPost('location')),
            'contact_info' => trim((string) $this->request->getPost('contact_info')),
            'status'       => $this->request->getPost('status'),
        ];

        if (!$branchModel->save($data)) {
            $modelErrors = $branchModel->errors();
            $message = 'Unable to create branch right now. Please try again.';

            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $message,
                        'errors'  => $modelErrors,
                    ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $modelErrors)
                ->with('error', $message);
        }

        return redirect()->to(site_url('branches'))
            ->with('success', 'Branch added successfully.');
    }

    //Show form the same time kuha branch id
    public function edit($id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $branchModel = new BranchModel();
        $data['branch'] = $branchModel->find($id);

        if (!$data['branch']) {
            return redirect()->to(site_url('branches'))
                ->with('error', 'Branch not found.');
        }

        return view('branchManager/editBranch', $data);
    }

    // Update dayun from edit form POST tapos update data           
    public function update($id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $rules = [
            'branch_name'  => 'required|min_length[3]|max_length[150]|regex_match[/^[A-Za-z0-9\s]+$/]',
            'location'     => 'required|min_length[3]|max_length[255]|regex_match[/^[A-Za-z0-9\s]+$/]',
            'contact_info' => 'permit_empty|regex_match[/^[0-9]{7,15}$/]',
            'status'       => 'required|in_list[existing,upcoming,franchise]',
        ];

        $messages = [
            'branch_name' => [
                'required'   => 'Branch name is required.',
                'min_length' => 'Branch name must be at least 3 characters.',
                'max_length' => 'Branch name may not exceed 150 characters.',
                'regex_match' => 'Branch name may only contain letters, numbers, and spaces.',
            ],
            'location' => [
                'required'   => 'Location is required.',
                'min_length' => 'Location must be at least 3 characters.',
                'max_length' => 'Location may not exceed 255 characters.',
                'regex_match' => 'Location may only contain letters, numbers, and spaces.',
            ],
            'contact_info' => [
                'regex_match' => 'Contact number must be 7 to 15 digits.',
            ],
            'status' => [
                'required' => 'Please select a status for the branch.',
                'in_list'  => 'Invalid status selected. Choose from existing, upcoming, or franchise.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            $message = 'Validation failed. Please review the highlighted fields.';

            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $message,
                        'errors'  => $errors,
                    ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $errors)
                ->with('error', $message);
        }

        $branchModel = new BranchModel();

        $data = [
            'branch_name'  => trim((string) $this->request->getPost('branch_name')),
            'location'     => trim((string) $this->request->getPost('location')),
            'contact_info' => trim((string) $this->request->getPost('contact_info')),
            'status'       => $this->request->getPost('status'),
        ];

        if (!$branchModel->update($id, $data)) {
            $modelErrors = $branchModel->errors();
            $message = 'Unable to update branch right now. Please try again.';

            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                    ->setJSON([
                        'status'  => 'error',
                        'message' => $message,
                        'errors'  => $modelErrors,
                    ]);
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $modelErrors)
                ->with('error', $message);
        }

        return redirect()->to(site_url('branches'))
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Delete a branch
     */
    public function delete($id)
    {
        if ($redirect = $this->authorize()) {
            return $redirect;
        }

        $branchModel = new BranchModel();
        $branchModel->delete($id);

        return redirect()->to(site_url('branches'))
            ->with('success', 'Branch deleted successfully.');
    }
}
