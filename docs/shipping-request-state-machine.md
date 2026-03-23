# Shipping Request State Machine

## Overview

The Shipping Request system now uses Spatie's Laravel Model States package to manage state transitions in a controlled and predictable way.

## States

The shipping request can be in one of the following states:

1. **PENDING** (Chờ xử lý) - Initial state when a shipping request is created
2. **PROCESSING** (Đang xử lý) - Request is being processed
3. **READY** (Sẵn sàng) - Request is ready for shipment
4. **SHIPPED** (Đã vận chuyển) - Request has been shipped
5. **DELIVERED** (Đã giao hàng) - Request has been delivered
6. **CANCELLED** (Đã hủy) - Request has been cancelled

## State Transitions

### Forward Progression
The normal workflow follows this sequence:
```
PENDING → PROCESSING → READY → SHIPPED → DELIVERED
```

### Cancellation Rules
- Can cancel from: **PENDING**, **PROCESSING**, **READY**
- Cannot cancel from: **SHIPPED**, **DELIVERED**

### Transition Rules
- **PENDING** can transition to:
  - PROCESSING (next step)
  - CANCELLED (cancellation)

- **PROCESSING** can transition to:
  - READY (next step)
  - CANCELLED (cancellation)

- **READY** can transition to:
  - SHIPPED (next step)
  - CANCELLED (cancellation)

- **SHIPPED** can transition to:
  - DELIVERED (next step)
  - Cannot be cancelled

- **DELIVERED** is a final state:
  - Cannot transition to any other state

- **CANCELLED** is a final state:
  - Cannot transition to any other state

## Usage in Code

### Moving to Next Step
```php
$shippingRequest = ShippingRequest::find(1);

if ($shippingRequest->canMoveToNextStep()) {
    $success = $shippingRequest->nextStep();
    if ($success) {
        // State transition successful
    }
}
```

### Cancelling a Request
```php
$shippingRequest = ShippingRequest::find(1);

if ($shippingRequest->canBeCancelled()) {
    $success = $shippingRequest->cancel();
    if ($success) {
        // Cancellation successful
    }
}
```

### Checking Current State
```php
$shippingRequest = ShippingRequest::find(1);

// Get state label
$label = $shippingRequest->status->label(); // e.g., "Chờ xử lý"

// Get state color
$color = $shippingRequest->status->color(); // e.g., "warning"

// Get state icon
$icon = $shippingRequest->status->icon(); // e.g., "heroicon-m-clock"

// Check specific state
if ($shippingRequest->status instanceof \App\States\PendingState) {
    // Handle pending state
}
```

## UI Integration

### Edit Page Actions
In the EditShippingRequest page, two new actions are available:

1. **Bước tiếp theo** (Next Step) - Moves the request to the next state in the workflow
2. **Hủy yêu cầu** (Cancel Request) - Cancels the request (only available for cancellable states)

These actions are conditionally visible based on the current state and available transitions.

### State Display
- Tables and info lists now display the state with appropriate colors and icons
- State badges are color-coded for easy visual identification
- Filters in tables work with the state machine

## Benefits

1. **Controlled Transitions**: Only valid state transitions are allowed
2. **Business Logic Enforcement**: Prevents invalid operations (e.g., cancelling delivered orders)
3. **Audit Trail**: State changes can be tracked and logged
4. **UI Consistency**: Consistent state display across all interfaces
5. **Type Safety**: State-specific methods and properties are available