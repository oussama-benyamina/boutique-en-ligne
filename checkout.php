<?php
session_start();
require 'functions/db_conn.php';
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51Q7ZrvRqnrD4rjze7vQoTVAGJI8iMEvDdLSpx56pXqzptoiuqIRMKpaWVfaQMY6r7khJjZC5N0oC5ryNWgVXL7a700xUsYPRcP');

$totalProductAmount = 0;
$cartItems = [];
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $sql = "SELECT * FROM products WHERE product_id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_keys($_SESSION['cart']));
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cartItems as $item) {
        $totalProductAmount += $item['price'] * $_SESSION['cart'][$item['product_id']];
    }
}

$shippingCost = 45;
if ($totalProductAmount > 500) {
    $shippingCost = 0;
}

$totalAmount = $totalProductAmount + $shippingCost;
$TVA_percentage = 0.20;
$TVA = $totalAmount * $TVA_percentage;
$finalTotalAmount = $totalAmount + $TVA;

$clientSecret = null;
if (isset($_SESSION['client_id'])) {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => round($finalTotalAmount * 100),
        'currency' => 'usd',
    ]);
    $clientSecret = $paymentIntent->client_secret;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/_head-index.php'; ?>
    <?php if (isset($_SESSION['client_id'])): ?>
    <script src="https://js.stripe.com/v3/"></script>
    <?php endif; ?>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Secure Payment</p>
                        <h1>Checkout</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="checkout-section mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="checkout-accordion-wrap">
                        <div class="accordion" id="accordionExample">
                            <?php if (!isset($_SESSION['client_id'])): ?>
                            <div class="card single-accordion">
                                <div class="card-header" id="headingRegistration">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseRegistration" aria-expanded="true" aria-controls="collapseRegistration">
                                        Registration
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseRegistration" class="collapse show" aria-labelledby="headingRegistration" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <div class="registration-form">
                                            <form id="registration-form">
                                                <p><input type="text" name="first_name" placeholder="First Name" required></p>
                                                <p><input type="text" name="last_name" placeholder="Last Name" required></p>
                                                <p><input type="email" name="email" placeholder="Email" required></p>
                                                <p><input type="password" name="password" placeholder="Password (min 8 chars, 1 uppercase, 1 number)" required></p>
                                                <p><input type="password" name="repeat_password" placeholder="Repeat Password" required></p>
                                                <p><input type="tel" name="phone_number" placeholder="Phone Number" required></p>
                                                <p><input type="text" name="address" placeholder="Address" required></p>
                                                <p><input type="text" name="city" placeholder="City" required></p>
                                                <p><input type="text" name="postal_code" placeholder="Postal Code" required></p>
                                                <p><input type="text" name="country" placeholder="Country" required></p>
                                                <button type="submit" class="boxed-btn">Register</button>
                                            </form>
                                            <div id="registration-message"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="card single-accordion">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Shipping Address
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseOne" class="collapse <?php echo isset($_SESSION['client_id']) ? 'show' : ''; ?>" aria-labelledby="headingOne" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <div class="shipping-address-form">
                                            <?php if (isset($_SESSION['client_id'])): ?>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="use_registered_address">
                                                <label class="form-check-label" for="use_registered_address">
                                                    Use my registered address
                                                </label>
                                            </div>
                                            <?php endif; ?>
                                            <form id="shipping-form">
                                                <p><input type="text" name="sp_name" id="sp_name" placeholder="Name" required></p>
                                                <p><input type="email" name="sp_email" id="sp_email" placeholder="Email" required></p>
                                                <p><input type="text" name="sp_address" id="sp_address" placeholder="Address" required></p>
                                                <p><input type="tel" name="sp_mobile" id="sp_mobile" placeholder="Phone" required></p>
                                                <p><input type="text" name="city" id="sp_city" placeholder="City" required></p>
                                                <p><input type="text" name="postal_code" id="sp_postal_code" placeholder="Postal Code" required></p>
                                                <p><input type="text" name="country" id="sp_country" placeholder="Country" required></p>
                                                <button id="process-payment-btn" class="boxed-btn mt-3">Process Payment</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (isset($_SESSION['client_id'])): ?>
                            <div class="card single-accordion">
                                <div class="card-header" id="headingThree">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Card Details
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <div class="card-details">
                                            <form id="payment-form">
                                                <div id="payment-element">
                                                    <!-- Stripe.js will insert the payment element here -->
                                                </div>
                                                <button id="submit" class="boxed-btn" style="border:none; margin-top:5px;">Pay Now</button>
                                                <div id="error-message"></div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-details-wrap">
                        <table class="order-details">
                            <thead>
                                <tr>
                                    <th>Your order Details</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['name']) ?> (<?= $_SESSION['cart'][$item['product_id']] ?>x)</td>
                                        <td>$<?= number_format($item['price'] * $_SESSION['cart'][$item['product_id']], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td>Subtotal</td>
                                    <td>$<?= number_format($totalAmount - $shippingCost, 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Shipping</td>
                                    <td>$<?= number_format($shippingCost, 2) ?></td>
                                </tr>
                                <tr>
                                    <td>TVA (20%)</td>
                                    <td>$<?= number_format($TVA, 2) ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><strong>$<?= number_format($finalTotalAmount, 2) ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/_footer.php'; ?>
    <?php include 'includes/_register-login.php'; ?>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const registrationForm = document.getElementById('registration-form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(registrationForm);

            try {
                const response = await fetch('functions/register.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    alert(result.messages[0]);
                    window.location.reload();
                } else {
                    alert(result.messages.join('\n'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    }

    const useRegisteredAddressCheckbox = document.getElementById('use_registered_address');
    if (useRegisteredAddressCheckbox) {
        useRegisteredAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                fetchUserAddress();
            } else {
                clearShippingForm();
            }
        });
    }

    function fetchUserAddress() {
        fetch('functions/get_user_address.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateShippingForm(data.address);
                } else {
                    alert('Error fetching address: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function populateShippingForm(address) {
        document.getElementById('sp_name').value = address.first_name + ' ' + address.last_name;
        document.getElementById('sp_email').value = address.email;
        document.getElementById('sp_address').value = address.address;
        document.getElementById('sp_mobile').value = address.phone_number;
        document.getElementById('sp_city').value = address.city;
        document.getElementById('sp_postal_code').value = address.postal_code;
        document.getElementById('sp_country').value = address.country;
    }

    function clearShippingForm() {
        document.getElementById('shipping-form').reset();
    }

    const processPaymentBtn = document.getElementById('process-payment-btn');
    const cardDetailsSection = document.getElementById('collapseThree');
    const cardDetailsButton = document.querySelector('[data-target="#collapseThree"]');

    if (processPaymentBtn && cardDetailsSection && cardDetailsButton) {
        processPaymentBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const shippingForm = document.getElementById('shipping-form');
            if (shippingForm.checkValidity()) {
                if (cardDetailsSection.classList.contains('collapse')) {
                    cardDetailsButton.click();
                }
            } else {
                shippingForm.reportValidity();
            }
        });
    }
});

<?php if (isset($_SESSION['client_id'])): ?>
const stripe = Stripe('pk_test_51Q7ZrvRqnrD4rjzemTskbjjY21UaYTmJv3IdEde2Mr0H7519FqtShWSRpGcMUxBxfaajrQ6V6IHb26aMmU5wwAnL00Lfhx7rEM');
const elements = stripe.elements({clientSecret: '<?php echo $clientSecret; ?>'});
const paymentElement = elements.create('payment');
paymentElement.mount('#payment-element');

const form = document.getElementById('payment-form');
form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const shippingForm = document.getElementById('shipping-form');
    const shippingData = new FormData(shippingForm);

    try {
        const response = await fetch('functions/save_shipping.php', {
            method: 'POST',
            body: shippingData
        });
        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message);
        }
    } catch (error) {
        document.querySelector('#error-message').textContent = 'Error saving shipping information: ' + error.message;
        return;
    }

    const {error} = await stripe.confirmPayment({
        elements,
        confirmParams: {
            return_url: 'http://localhost/boutiaue_plateforme/functions/payment-confirmation.php',
        },
    });

    if (error) {
        const messageContainer = document.querySelector('#error-message');
        messageContainer.textContent = error.message;
    }
});
<?php endif; ?>
</script>
</body>
</html>