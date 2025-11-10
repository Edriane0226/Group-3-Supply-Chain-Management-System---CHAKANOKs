<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\BranchModel;
use App\Models\PurchaseRequestModel;


class Dashboard extends Controller
{
    public function index()
    {
        $session = session();

        //  Check if logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        $branchName = $session->get('branch_name');
        $userModel = new UserModel();

        if ($session->get('role') == 'Branch Manager') {

            //Add dashboard data kung naa i dagdag

            // Get all user in the same branch sa branch manager na naka login
            $allUsers = $userModel->getUserByBranch($session->get('branch_id'));
            $data = [
                'branchName' => $branchName,
                'allUsers' => $allUsers,
                'role' => $session->get('role'),
            ];

            return view('pages/dashboard', $data);
        }

        else if ($session->get('role') == 'Central Office Admin') {

             //Add dashboard data kung naa i dagdag

            // Get all users kay Central Office
            $allUsers = $userModel->findAll();
            $data = [
                'branchName' => $branchName,
                'allUsers' => $allUsers,
                'role' => $session->get('role'),
            ];

            // Date range filter (7d/30d)
            $range = $this->request->getGet('range') === '30d' ? '30d' : '7d';
            $sinceDate = $range === '30d' ? date('Y-m-d H:i:s', strtotime('-30 days')) : date('Y-m-d H:i:s', strtotime('-7 days'));
            $data['range'] = $range;

            // Basic KPI data for Central Office Admin
            $branchModel = new BranchModel();
            $purchModel = new PurchaseRequestModel();

            // Sales summary placeholder: use PR counts as proxy
            $totalPRs = $purchModel->where('created_at >=', $sinceDate)->countAllResults();
            $pendingPRs = $purchModel->where('status', 'pending')->where('created_at >=', $sinceDate)->countAllResults();
            $approvedPRs = $purchModel->where('status', 'approved')->where('created_at >=', $sinceDate)->countAllResults();
            $cancelledPRs = $purchModel->where('status', 'cancelled')->where('created_at >=', $sinceDate)->countAllResults();

            $data['sales_summary'] = "Total Requests: $totalPRs | Pending: $pendingPRs | Approved: $approvedPRs | Cancelled: $cancelledPRs";

            // Branches list HTML
            $branches = $branchModel->findAll();
            $branches_list = '<ul class="list-group list-group-flush">';
            foreach ($branches as $b) {
                $name = esc($b['branch_name']);
                $status = esc($b['status'] ?? 'active');
                $bid = (int)($b['id'] ?? 0);
                $link = site_url('purchase-requests?branch=' . $bid);
                $branches_list .= "<li class=\"list-group-item d-flex justify-content-between\"><span><a href=\"$link\" class=\"text-decoration-none\">$name</a></span><span class=\"badge bg-secondary\">$status</span></li>";
            }
            $branches_list .= '</ul>';
            $data['branches_list'] = $branches_list;

            // Branch performance simple table by pending PRs
            $builder = $purchModel->select('branches.id as branch_id, branches.branch_name, COUNT(purchase_requests.id) as pending_count')
                                  ->join('branches', 'branches.id = purchase_requests.branch_id', 'left')
                                  ->where('purchase_requests.status', 'pending')
                                  ->where('purchase_requests.created_at >=', $sinceDate)
                                  ->groupBy('purchase_requests.branch_id')
                                  ->orderBy('pending_count', 'DESC')
                                  ->get();
            $rows = $builder->getResultArray();
            $chart = '<table class="table table-sm"><thead><tr><th>Branch</th><th>Pending PRs</th></tr></thead><tbody>';
            foreach ($rows as $r) {
                $bid = (int)($r['branch_id'] ?? 0);
                $plink = site_url('purchase-requests?branch=' . $bid . '&status=pending');
                $chart .= '<tr><td><a href="'.$plink.'" class="text-decoration-none">'.esc($r['branch_name'] ?? 'N/A').'</a></td><td>'.(int)($r['pending_count'] ?? 0).'</td></tr>';
            }
            $chart .= '</tbody></table>';
            $data['branch_performance_chart'] = $chart;

            // Top branches by approvals (range)
            $topApproved = $purchModel->select('branches.id as branch_id, branches.branch_name, COUNT(purchase_requests.id) as approved_count')
                                      ->join('branches', 'branches.id = purchase_requests.branch_id', 'left')
                                      ->where('purchase_requests.status', 'approved')
                                      ->where('purchase_requests.created_at >=', $sinceDate)
                                      ->groupBy('purchase_requests.branch_id')
                                      ->orderBy('approved_count', 'DESC')
                                      ->limit(5)
                                      ->get()
                                      ->getResultArray();
            $topTbl = '<table class="table table-sm mb-0"><thead><tr><th>Branch</th><th>Approved</th></tr></thead><tbody>';
            foreach ($topApproved as $r) {
                $bid = (int)($r['branch_id'] ?? 0);
                $alink = site_url('purchase-requests?branch=' . $bid . '&status=approved');
                $topTbl .= '<tr><td><a href="'.$alink.'" class="text-decoration-none">'.esc($r['branch_name'] ?? 'N/A').'</a></td><td>'.(int)($r['approved_count'] ?? 0).'</td></tr>';
            }
            $topTbl .= '</tbody></table>';
            $data['top_approvals'] = $topTbl;

            // Late deliveries (Delivered after ETA)
            $db = \Config\Database::connect();
            $late = $db->table('deliveries')
                       ->select('branches.id as branch_id, branches.branch_name, COUNT(*) as late_cnt')
                       ->join('branches', 'branches.id = deliveries.branch_id', 'left')
                       ->where('deliveries.status', 'Delivered')
                       ->where('deliveries.delivery_date >=', $sinceDate)
                       ->where('deliveries.actual_delivery_time > deliveries.estimated_eta')
                       ->groupBy('deliveries.branch_id')
                       ->orderBy('late_cnt', 'DESC')
                       ->limit(5)
                       ->get()
                       ->getResultArray();
            $lateTbl = '<table class="table table-sm mb-0"><thead><tr><th>Branch</th><th>Late</th></tr></thead><tbody>';
            foreach ($late as $r) {
                $bid = (int)($r['branch_id'] ?? 0);
                $lateTbl .= '<tr><td>'.esc($r['branch_name'] ?? 'N/A').'</td><td>'.(int)($r['late_cnt'] ?? 0).'</td></tr>';
            }
            $lateTbl .= '</tbody></table>';
            $data['late_deliveries'] = $lateTbl;

            // Reports section with quick links
            $data['reports_section'] = '<a class="btn btn-sm btn-outline-primary" href="'.site_url('inventory/reports').'">Inventory Reports</a>';

            // Delivery status summary (group by status)
            $delStatuses = $db->table('deliveries')
                              ->select('status, COUNT(*) as cnt')
                              ->where('delivery_date >=', $sinceDate)
                              ->groupBy('status')
                              ->get()
                              ->getResultArray();
            $delivery_html = '<ul class="list-unstyled mb-0">';
            foreach ($delStatuses as $st) {
                $delivery_html .= '<li>'.esc($st['status']).': <strong>'.(int)$st['cnt'].'</strong></li>';
            }
            $delivery_html .= '</ul>';
            $data['delivery_status'] = $delivery_html;

            // Sales breakdown placeholder
            $data['salesBreakdown'] = "Approved: $approvedPRs | Pending: $pendingPRs | Cancelled: $cancelledPRs";
            
            return view('pages/dashboard', $data);
        }

        else if ($session->get('role') == 'Inventory Staff') {
           
            $role = $session->get('role');
            $data = [
                'role' => $role,
            ];

            // Access Lng niya is Inventory Overview
            return view('pages/inventory_overview', $data);
        }


        // //  Only Branch Managers can access
        // if ($session->get('role') !== 'Branch Manager') {
        //     return redirect()->to(site_url('login'))->with('error', 'Unauthorized access.');
        // }
        
        // //  Load dashboard view
        // return view('pages/branchdashboard', [
        //     'full_name'   => $session->get('full_name'),
        //     'branch_name' => $session->get('branch_name'),
            
        // ]);
    }
}
