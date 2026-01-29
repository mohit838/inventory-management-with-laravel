#!/bin/bash

# Login and get token
echo "Logging in..."
LOGIN_RESPONSE=$(curl -s -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"superadmin@test.com", "password":"password"}')

TOKEN=$(echo $LOGIN_RESPONSE | grep -oP '"access_token":"\K[^"]+')

if [ -z "$TOKEN" ]; then
    echo "Login failed. Response: $LOGIN_RESPONSE"
    exit 1
fi

echo "Login successful."

# List Orders
echo "Listing Orders..."
ORDERS_RESPONSE=$(curl -s -X GET http://localhost:8000/api/v1/orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json")

echo "$ORDERS_RESPONSE" | head -c 500 # Print first 500 chars

# Extract first order ID (simple regex approximation)
FIRST_ORDER_ID=$(echo $ORDERS_RESPONSE | grep -oP '"id":\K\d+' | head -1)

if [ -n "$FIRST_ORDER_ID" ]; then
    echo ""
    echo "Getting details for Order #$FIRST_ORDER_ID..."
    curl -s -X GET "http://localhost:8000/api/v1/orders/$FIRST_ORDER_ID" \
      -H "Authorization: Bearer $TOKEN" \
      -H "Content-Type: application/json"
else
    echo "No orders found to verify details."
fi

echo ""
echo "Verifying Dashboard Summary..."
curl -s -X GET http://localhost:8000/api/v1/dashboard/summary \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" | head -c 500

echo ""
echo ""
echo "Verifying Dashboard Chart..."
curl -s -X GET http://localhost:8000/api/v1/dashboard/chart?period=monthly \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" | head -c 500

