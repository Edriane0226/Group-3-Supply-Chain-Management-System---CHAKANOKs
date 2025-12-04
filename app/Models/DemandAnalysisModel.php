<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Demand Analysis Model
 * 
 * Analyzes demand patterns based on purchase requests and orders.
 * Note: This is based on purchase patterns (proxy for demand).
 * Can be upgraded to actual sales-based analysis when sales data is available.
 */
class DemandAnalysisModel extends Model
{
    protected $table = null; // No single table, uses multiple tables
    protected $primaryKey = null;

    /**
     * Get demand summary (overall statistics)
     */
    public function getDemandSummary(): array
    {
        // Total purchase requests (demand signals)
        $totalRequests = $this->db->table('purchase_requests')
            ->where('status !=', 'cancelled')
            ->countAllResults(false);

        // Total items requested
        $totalItemsRequested = $this->db->table('purchase_requests')
            ->selectSum('quantity')
            ->where('status !=', 'cancelled')
            ->get()
            ->getRowArray();

        // Total approved orders (confirmed demand)
        $totalOrders = $this->db->table('purchase_orders')
            ->whereIn('status', ['Approved', 'Delivered', 'In_Transit'])
            ->countAllResults(false);

        // Unique items in demand
        $uniqueItems = $this->db->table('purchase_requests')
            ->select('item_name')
            ->distinct()
            ->where('status !=', 'cancelled')
            ->countAllResults(false);

        // Average request frequency (requests per day in last 30 days)
        $avgFrequency = $this->getAverageRequestFrequency(30);

        return [
            'total_requests' => $totalRequests,
            'total_items_requested' => (int)($totalItemsRequested['quantity'] ?? 0),
            'total_orders' => $totalOrders,
            'unique_items' => $uniqueItems,
            'avg_request_frequency' => round($avgFrequency, 2),
        ];
    }

    /**
     * Get demand by branch
     */
    public function getDemandByBranch(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = "
            SELECT
                branches.id,
                branches.branch_name,
                COUNT(DISTINCT purchase_requests.id) as total_requests,
                SUM(purchase_requests.quantity) as total_items_requested,
                COUNT(DISTINCT purchase_requests.item_name) as unique_items,
                AVG(purchase_requests.quantity) as avg_quantity_per_request,
                COUNT(DISTINCT CASE WHEN purchase_requests.status = 'approved' THEN purchase_orders.id END) as total_orders
            FROM purchase_requests
            JOIN branches ON branches.id = purchase_requests.branch_id
            LEFT JOIN purchase_orders ON purchase_orders.purchase_request_id = purchase_requests.id
            WHERE purchase_requests.status != 'cancelled'
        ";

        $params = [];
        if ($dateFrom) {
            $query .= " AND purchase_requests.created_at >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $query .= " AND purchase_requests.created_at <= ?";
            $params[] = $dateTo . ' 23:59:59';
        }

        $query .= " GROUP BY branches.id, branches.branch_name ORDER BY total_requests DESC";

        return $this->db->query($query, $params)->getResultArray();
    }

    /**
     * Get fast/slow moving items
     * Fast moving = frequently requested items
     * Slow moving = rarely requested items
     */
    public function getFastSlowMovingItems(int $limit = 20): array
    {
        $query = "
            SELECT
                purchase_requests.item_name,
                COUNT(DISTINCT purchase_requests.id) as request_count,
                SUM(purchase_requests.quantity) as total_quantity_requested,
                AVG(purchase_requests.quantity) as avg_quantity,
                COUNT(DISTINCT purchase_requests.branch_id) as branches_requesting,
                MAX(purchase_requests.created_at) as last_requested,
                MIN(purchase_requests.created_at) as first_requested,
                DATEDIFF(MAX(purchase_requests.created_at), MIN(purchase_requests.created_at)) as days_span,
                CASE 
                    WHEN COUNT(DISTINCT purchase_requests.id) >= 10 THEN 'Fast Moving'
                    WHEN COUNT(DISTINCT purchase_requests.id) >= 5 THEN 'Medium Moving'
                    ELSE 'Slow Moving'
                END as movement_category
            FROM purchase_requests
            WHERE purchase_requests.status != 'cancelled'
            GROUP BY purchase_requests.item_name
            ORDER BY request_count DESC, total_quantity_requested DESC
            LIMIT {$limit}
        ";

        return $this->db->query($query)->getResultArray();
    }

    /**
     * Get demand trends (daily request count for last N days)
     */
    public function getDemandTrends(int $days = 30): array
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $query = "
            SELECT
                DATE(created_at) as date,
                COUNT(*) as request_count,
                SUM(quantity) as total_quantity,
                COUNT(DISTINCT item_name) as unique_items
            FROM purchase_requests
            WHERE created_at >= ?
                AND status != 'cancelled'
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ";

        return $this->db->query($query, [$startDate])->getResultArray();
    }

    /**
     * Get demand by item (top items by request frequency)
     */
    public function getDemandByItem(int $limit = 15, ?int $branchId = null): array
    {
        $whereClause = "WHERE purchase_requests.status != 'cancelled'";
        $params = [];

        if ($branchId) {
            $whereClause .= " AND purchase_requests.branch_id = ?";
            $params[] = $branchId;
        }

        $query = "
            SELECT
                purchase_requests.item_name,
                COUNT(DISTINCT purchase_requests.id) as request_count,
                SUM(purchase_requests.quantity) as total_quantity_requested,
                AVG(purchase_requests.quantity) as avg_quantity,
                COUNT(DISTINCT purchase_requests.branch_id) as branches_requesting,
                MAX(purchase_requests.created_at) as last_requested
            FROM purchase_requests
            {$whereClause}
            GROUP BY purchase_requests.item_name
            ORDER BY request_count DESC, total_quantity_requested DESC
            LIMIT {$limit}
        ";

        return $this->db->query($query, $params)->getResultArray();
    }

    /**
     * Get reorder point analysis
     * Compares current stock with average demand frequency
     */
    public function getReorderPointAnalysis(?int $branchId = null): array
    {
        // Get current stock levels
        $stockQuery = "
            SELECT
                si.item_name,
                si.branch_id,
                branches.branch_name,
                SUM(si.quantity) - IFNULL(SUM(so.quantity), 0) AS current_stock,
                si.unit
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
            JOIN branches ON branches.id = si.branch_id
        ";

        $stockParams = [];
        if ($branchId) {
            $stockQuery .= " WHERE si.branch_id = ?";
            $stockParams[] = $branchId;
        }

        $stockQuery .= " GROUP BY si.item_name, si.branch_id, branches.branch_name, si.unit";
        $stockData = $this->db->query($stockQuery, $stockParams)->getResultArray();

        // Get demand frequency per item
        $demandQuery = "
            SELECT
                purchase_requests.item_name,
                purchase_requests.branch_id,
                COUNT(DISTINCT purchase_requests.id) as request_count,
                AVG(purchase_requests.quantity) as avg_quantity_requested,
                DATEDIFF(MAX(purchase_requests.created_at), MIN(purchase_requests.created_at)) as days_span,
                CASE 
                    WHEN DATEDIFF(MAX(purchase_requests.created_at), MIN(purchase_requests.created_at)) > 0 
                    THEN COUNT(DISTINCT purchase_requests.id) / DATEDIFF(MAX(purchase_requests.created_at), MIN(purchase_requests.created_at)) * 30
                    ELSE 0
                END as requests_per_month
            FROM purchase_requests
            WHERE purchase_requests.status != 'cancelled'
        ";

        $demandParams = [];
        if ($branchId) {
            $demandQuery .= " AND purchase_requests.branch_id = ?";
            $demandParams[] = $branchId;
        }

        $demandQuery .= " GROUP BY purchase_requests.item_name, purchase_requests.branch_id";
        $demandData = $this->db->query($demandQuery, $demandParams)->getResultArray();

        // Create lookup for demand data
        $demandLookup = [];
        foreach ($demandData as $demand) {
            $key = $demand['item_name'] . '_' . $demand['branch_id'];
            $demandLookup[$key] = $demand;
        }

        // Combine stock and demand data
        $results = [];
        foreach ($stockData as $stock) {
            $key = $stock['item_name'] . '_' . $stock['branch_id'];
            $demand = $demandLookup[$key] ?? null;

            $currentStock = (int)$stock['current_stock'];
            $avgQuantity = $demand ? (float)$demand['avg_quantity_requested'] : 0;
            $requestsPerMonth = $demand ? (float)$demand['requests_per_month'] : 0;

            // Calculate suggested reorder point (2 months of average demand)
            $suggestedReorderPoint = max(10, round($avgQuantity * 2));

            // Calculate days of stock remaining (if demand exists)
            $daysOfStock = $requestsPerMonth > 0 ? round(($currentStock / $avgQuantity) * 30) : null;

            $results[] = [
                'item_name' => $stock['item_name'],
                'branch_id' => $stock['branch_id'],
                'branch_name' => $stock['branch_name'],
                'current_stock' => $currentStock,
                'unit' => $stock['unit'],
                'avg_quantity_requested' => round($avgQuantity, 2),
                'requests_per_month' => round($requestsPerMonth, 2),
                'suggested_reorder_point' => $suggestedReorderPoint,
                'days_of_stock' => $daysOfStock,
                'status' => $currentStock <= $suggestedReorderPoint ? 'Low Stock' : 'Adequate',
            ];
        }

        // Sort by days of stock (lowest first)
        usort($results, function($a, $b) {
            if ($a['days_of_stock'] === null && $b['days_of_stock'] === null) return 0;
            if ($a['days_of_stock'] === null) return 1;
            if ($b['days_of_stock'] === null) return -1;
            return $a['days_of_stock'] <=> $b['days_of_stock'];
        });

        return array_slice($results, 0, 20); // Top 20 items needing attention
    }

    /**
     * Get demand vs supply comparison
     * Compares current stock with average demand
     */
    public function getDemandVsSupply(?int $branchId = null): array
    {
        // Get average monthly demand per item
        $demandQuery = "
            SELECT
                purchase_requests.item_name,
                purchase_requests.branch_id,
                AVG(purchase_requests.quantity) as avg_monthly_demand,
                COUNT(DISTINCT purchase_requests.id) as request_count,
                SUM(purchase_requests.quantity) as total_demand
            FROM purchase_requests
            WHERE purchase_requests.status != 'cancelled'
                AND purchase_requests.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
        ";

        $demandParams = [];
        if ($branchId) {
            $demandQuery .= " AND purchase_requests.branch_id = ?";
            $demandParams[] = $branchId;
        }

        $demandQuery .= " GROUP BY purchase_requests.item_name, purchase_requests.branch_id";
        $demandData = $this->db->query($demandQuery, $demandParams)->getResultArray();

        // Get current stock
        $stockQuery = "
            SELECT
                si.item_name,
                si.branch_id,
                branches.branch_name,
                SUM(si.quantity) - IFNULL(SUM(so.quantity), 0) AS current_stock,
                si.unit
            FROM stock_in si
            LEFT JOIN stock_out so
                ON si.item_type_id = so.item_type_id
                AND si.branch_id = so.branch_id
                AND si.item_name = so.item_name
            JOIN branches ON branches.id = si.branch_id
        ";

        $stockParams = [];
        if ($branchId) {
            $stockQuery .= " WHERE si.branch_id = ?";
            $stockParams[] = $branchId;
        }

        $stockQuery .= " GROUP BY si.item_name, si.branch_id, branches.branch_name, si.unit";
        $stockData = $this->db->query($stockQuery, $stockParams)->getResultArray();

        // Create lookup
        $stockLookup = [];
        foreach ($stockData as $stock) {
            $key = $stock['item_name'] . '_' . $stock['branch_id'];
            $stockLookup[$key] = $stock;
        }

        // Combine data
        $results = [];
        foreach ($demandData as $demand) {
            $key = $demand['item_name'] . '_' . $demand['branch_id'];
            $stock = $stockLookup[$key] ?? null;

            $currentStock = $stock ? (int)$stock['current_stock'] : 0;
            $avgMonthlyDemand = (float)$demand['avg_monthly_demand'];
            $monthsOfSupply = $avgMonthlyDemand > 0 ? round($currentStock / $avgMonthlyDemand, 1) : null;

            $results[] = [
                'item_name' => $demand['item_name'],
                'branch_id' => $demand['branch_id'],
                'branch_name' => $stock['branch_name'] ?? 'N/A',
                'current_stock' => $currentStock,
                'unit' => $stock['unit'] ?? 'pcs',
                'avg_monthly_demand' => round($avgMonthlyDemand, 2),
                'total_demand' => (int)$demand['total_demand'],
                'request_count' => (int)$demand['request_count'],
                'months_of_supply' => $monthsOfSupply,
                'status' => $monthsOfSupply !== null ? ($monthsOfSupply < 1 ? 'Critical' : ($monthsOfSupply < 2 ? 'Low' : 'Adequate')) : 'No Stock',
            ];
        }

        // Sort by months of supply (lowest first)
        usort($results, function($a, $b) {
            if ($a['months_of_supply'] === null && $b['months_of_supply'] === null) return 0;
            if ($a['months_of_supply'] === null) return 1;
            if ($b['months_of_supply'] === null) return -1;
            return $a['months_of_supply'] <=> $b['months_of_supply'];
        });

        return $results;
    }

    /**
     * Get seasonal patterns (if detectable)
     * Analyzes demand by month
     */
    public function getSeasonalPatterns(int $months = 12): array
    {
        $startDate = date('Y-m-d', strtotime("-{$months} months"));
        
        $query = "
            SELECT
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                MONTHNAME(created_at) as month_name,
                COUNT(*) as request_count,
                SUM(quantity) as total_quantity,
                COUNT(DISTINCT item_name) as unique_items
            FROM purchase_requests
            WHERE created_at >= ?
                AND status != 'cancelled'
            GROUP BY YEAR(created_at), MONTH(created_at), MONTHNAME(created_at)
            ORDER BY year ASC, month ASC
        ";

        return $this->db->query($query, [$startDate])->getResultArray();
    }

    /**
     * Calculate average request frequency (requests per day)
     */
    private function getAverageRequestFrequency(int $days = 30): float
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        
        $count = $this->db->table('purchase_requests')
            ->where('created_at >=', $startDate)
            ->where('status !=', 'cancelled')
            ->countAllResults(false);

        return $days > 0 ? $count / $days : 0;
    }
}

