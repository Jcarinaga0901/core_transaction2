<?php
/**
 * Notification Helper Functions
 * Use these functions to create notifications throughout the application
 */

/**
 * Create a notification for a user
 * 
 * @param PDO $pdo Database connection
 * @param int $userId User ID to send notification to
 * @param string $type Notification type (product_created, voucher_created, order_approved, etc.)
 * @param string $title Notification title
 * @param string $message Notification message
 * @param int|null $relatedId Related entity ID (product_id, voucher_id, order_id, etc.)
 * @param int|null $vendorId Optional vendor ID
 * @return int|false The notification ID or false on failure
 */
function createNotification($pdo, $userId, $type, $title, $message, $relatedId = null, $vendorId = null) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, vendor_id, type, title, message, related_id, is_read, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 0, NOW())
        ");
        
        $stmt->execute([
            $userId,
            $vendorId,
            $type,
            $title,
            $message,
            $relatedId
        ]);
        
        return (int)$pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Failed to create notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Create notification when a product is created
 */
function notifyProductCreated($pdo, $userId, $productId, $productName, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'product_created',
        'Product Created',
        "Your product '$productName' has been created and is pending approval.",
        $productId,
        $vendorId
    );
}

/**
 * Create notification when a product is approved
 */
function notifyProductApproved($pdo, $userId, $productId, $productName, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'product_approved',
        'Product Approved',
        "Your product '$productName' has been approved and is now live!",
        $productId,
        $vendorId
    );
}

/**
 * Create notification when a product is rejected
 */
function notifyProductRejected($pdo, $userId, $productId, $productName, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'product_rejected',
        'Product Rejected',
        "Your product '$productName' has been rejected. Please review and resubmit.",
        $productId,
        $vendorId
    );
}

/**
 * Create notification when a voucher is created
 */
function notifyVoucherCreated($pdo, $userId, $voucherId, $voucherName, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'voucher_created',
        'Voucher Created',
        "Your voucher '$voucherName' has been created and is pending approval.",
        $voucherId,
        $vendorId
    );
}

/**
 * Create notification when a voucher is approved
 */
function notifyVoucherApproved($pdo, $userId, $voucherId, $voucherName, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'voucher_approved',
        'Voucher Approved',
        "Your voucher '$voucherName' has been approved!",
        $voucherId,
        $vendorId
    );
}

/**
 * Create notification when a voucher is rejected
 */
function notifyVoucherRejected($pdo, $userId, $voucherId, $voucherName, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'voucher_rejected',
        'Voucher Rejected',
        "Your voucher '$voucherName' has been rejected.",
        $voucherId,
        $vendorId
    );
}

/**
 * Create notification for new order
 */
function notifyNewOrder($pdo, $userId, $orderId, $orderNumber, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'new_order',
        'New Order Received',
        "You have received a new order #$orderNumber",
        $orderId,
        $vendorId
    );
}

/**
 * Create notification when order is approved
 */
function notifyOrderApproved($pdo, $userId, $orderId, $orderNumber, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'order_approved',
        'Order Approved',
        "Order #$orderNumber has been approved and is being processed.",
        $orderId,
        $vendorId
    );
}

/**
 * Create notification for low stock
 */
function notifyLowStock($pdo, $userId, $productId, $productName, $stockLevel, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'low_stock',
        'Low Stock Alert',
        "Product '$productName' is running low on stock (only $stockLevel remaining).",
        $productId,
        $vendorId
    );
}

/**
 * Create notification for product review
 */
function notifyNewReview($pdo, $userId, $productId, $productName, $rating, $vendorId = null) {
    $stars = str_repeat('⭐', $rating);
    return createNotification(
        $pdo,
        $userId,
        'new_review',
        'New Product Review',
        "Your product '$productName' received a new review: $stars",
        $productId,
        $vendorId
    );
}

/**
 * Create notification when order status changes
 */
function notifyOrderStatusChange($pdo, $userId, $orderId, $oldStatus, $newStatus, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'order_status_updated',
        'Order Status Updated',
        "Your order #$orderId status has been updated from '$oldStatus' to '$newStatus'.",
        $orderId,
        $vendorId
    );
}

/**
 * Create notification when order is cancelled
 */
function notifyOrderCancelled($pdo, $userId, $orderId, $reason = '', $vendorId = null) {
    $message = "Your order #$orderId has been cancelled.";
    if ($reason) {
        $message .= " Reason: $reason";
    }
    return createNotification(
        $pdo,
        $userId,
        'order_cancelled',
        'Order Cancelled',
        $message,
        $orderId,
        $vendorId
    );
}

/**
 * Create notification when order is delivered
 */
function notifyOrderDelivered($pdo, $userId, $orderId, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'order_delivered',
        'Order Delivered',
        "Great news! Your order #$orderId has been successfully delivered.",
        $orderId,
        $vendorId
    );
}

/**
 * Create notification when cancellation is approved
 */
function notifyCancellationApproved($pdo, $userId, $orderId, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'cancellation_approved',
        'Cancellation Approved',
        "Your cancellation request for order #$orderId has been approved.",
        $orderId,
        $vendorId
    );
}

/**
 * Create notification when cancellation is rejected
 */
function notifyCancellationRejected($pdo, $userId, $orderId, $vendorId = null) {
    return createNotification(
        $pdo,
        $userId,
        'cancellation_rejected',
        'Cancellation Rejected',
        "Your cancellation request for order #$orderId has been rejected. The order will continue processing.",
        $orderId,
        $vendorId
    );
}

/**
 * Create notification when documents are uploaded successfully
 */
function notifyDocumentsUploaded($pdo, $userId, $documentTypes, $vendorId = null) {
    $types = is_array($documentTypes) ? implode(', ', $documentTypes) : $documentTypes;
    return createNotification(
        $pdo,
        $userId,
        'documents_uploaded',
        'Documents Uploaded Successfully',
        "Documents uploaded successfully: $types",
        null,
        $vendorId
    );
}
?>

