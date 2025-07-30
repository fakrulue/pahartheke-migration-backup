<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Registration Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .form-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #343a40;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        .form-section label {
            font-weight: 500;
        }

        .btn-submit {
            background-color: #28a745;
            color: #fff;
            font-weight: bold;
            padding: 0.75rem;
            border-radius: 5px;
        }

        .btn-submit:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Affiliate Registration Form</h2>
            <form method="POST">
                <!-- Name -->
                <div class="form-group form-section">
                    <label for="full-name">Full Name</label>
                    <input type="text" id="full-name" name="full_name" class="form-control" placeholder="Enter your full name" required>
                </div>

                <!-- NID -->
                <div class="form-group form-section">
                    <label for="nid">National ID (NID)</label>
                    <input type="text" id="nid" name="nid" class="form-control" placeholder="Enter your National ID" required>
                </div>

                <!-- Phone -->
                <div class="form-group form-section">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" required>
                </div>

                <!-- Address -->
                <div class="form-section">
                    <label>Address</label>
                    <input type="text" class="form-control mb-2" name="address_street1" placeholder="Street Address" required>
                    <input type="text" class="form-control mb-2" name="address_street2" placeholder="Street Address Line 2">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control" name="address_city" placeholder="City" required>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control" name="address_state" placeholder="State / Province" required>
                        </div>
                    </div>
                    <input type="text" class="form-control" name="address_postal_code" placeholder="Postal / Zip Code" required>
                </div>

                <!-- Account Email -->
                <div class="form-group form-section">
                    <label for="email">Account Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="example@example.com" required>
                </div>


                <!-- Payment Method -->
                <div class="form-group form-section">
                    <label for="payment-method">Payment Method</label>
                    <select class="form-control" id="payment-method" name="payment_method" required>
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="bank">Bank</option>
                        <option value="mobile">Mobile Banking</option>
                    </select>
                </div>

                <!-- Bank Fields -->
                <div id="bank-fields" class="form-group form-section" style="display: none;">
                    <label for="bank-name">Bank Name</label>
                    <input type="text" id="bank-name" name="bank_name" class="form-control mb-2" placeholder="Bank Name">
                    <label for="account-name"> Account Name</label>
                    <input type="text" id="account-name" name="account_name" class="form-control mb-2" placeholder="Account Name">
                    <label for="account-number">Account Number</label>
                    <input type="text" id="account-number" name="account_number" class="form-control mb-2" placeholder="Account Number">
                    <label for="branch-name">Branch Name</label>
                    <input type="text" id="branch-name" name="branch_name" class="form-control" placeholder="Branch Name">
                </div>

                <!-- Mobile Banking Fields -->
                <div id="mobile-banking-field" class="form-group form-section" style="display: none;">
                    <label for="mobile-provider">Mobile Banking Provider</label>
                    <select id="mobile-provider" name="mobile_provider" class="form-control">
                        <option value="" disabled selected>Select Provider</option>
                        <option value="bkash">bKash</option>
                        <option value="rocket">Rocket</option>
                        <option value="nagad">Nagad</option>
                    </select>
                    <label for="mobile-number">Mobile Banking Number</label>
                    <input type="text" id="mobile-number" name="mobile_number" class="form-control mb-2" placeholder="Mobile Banking Number">
                </div>

                <!-- Social Links Method -->
                <div class="form-group form-section">
                    <label for="facebook-link">Facebook Link</label>
                    <input type="url" id="facebook-link" name="facebook_link" class="form-control" placeholder="Enter your Facebook profile link">
                </div>
                <div class="form-group form-section">
                    <label for="instagram-link">Instagram Link</label>
                    <input type="url" id="instagram-link" name="instagram_link" class="form-control" placeholder="Enter your Instagram profile link">
                </div>
                <div class="form-group form-section">
                    <label for="twitter-link">Youtube Link</label>
                    <input type="url" id="youtube-link" name="youtube_link" class="form-control" placeholder="Enter your Youtube profile link">
                </div>
                <div class="form-group form-section">
                    <label for="linkedin-link">LinkedIn Link</label>
                    <input type="url" id="linkedin-link" name="linkedin_link" class="form-control" placeholder="Enter your LinkedIn profile link">
                </div>
                <div class="form-group form-section">
                    <label for="website-link">Website Link</label>
                    <input type="url" id="website-link" name="website_link" class="form-control" placeholder="Enter your website link">
                </div>

                <!-- <div class="form-group form-section">
                    <label for="promotion-method">Social Links</label>
                    <textarea id="promotion-method" name="promotion_method" class="form-control" rows="4" placeholder="Describe your Social Links" required></textarea>
                </div> -->

                <!-- Nominee Details -->
                <div class="form-group form-section">
                    <label for="nominee-name">Nominee Name</label>
                    <input type="text" id="nominee-name" name="nominee_name" class="form-control" placeholder="Enter nominee's name" required>
                </div>
                <div class="form-group form-section">
                    <label for="nominee-phone">Nominee Phone</label>
                    <input type="text" id="nominee-phone" name="nominee_phone" class="form-control" placeholder="Enter nominee's phone number" required>
                </div>
                <div class="form-group form-section">
                    <label for="nominee-relation">Relation with Nominee</label>
                    <input type="text" id="nominee-relation" name="nominee_relation" class="form-control" placeholder="Enter your relation with the nominee" required>
                </div>

                <button type="submit" class="btn btn-submit btn-block">Submit</button>
            </form>
        </div>
    </div>
    <!-- SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('payment-method').addEventListener('change', function() {
            const bankFields = document.getElementById('bank-fields');
            const mobileField = document.getElementById('mobile-banking-field');
            if (this.value === 'bank') {
                bankFields.style.display = 'block';
                mobileField.style.display = 'none';
            } else if (this.value === 'mobile') {
                bankFields.style.display = 'none';
                mobileField.style.display = 'block';
            } else {
                bankFields.style.display = 'none';
                mobileField.style.display = 'none';
            }
        });

        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();

                // Collect form data
                const formData = {
                    full_name: $('#full-name').val(),
                    nid: $('#nid').val(),
                    phone: $('#phone').val(),
                    address_street1: $('input[placeholder="Street Address"]').eq(0).val(),
                    address_street2: $('input[placeholder="Street Address Line 2"]').val(),
                    address_city: $('input[placeholder="City"]').val(),
                    address_state: $('input[placeholder="State / Province"]').val(),
                    address_postal_code: $('input[placeholder="Postal / Zip Code"]').val(),
                    email: $('#email').val(),
                    payment_method: $('#payment-method').val(),
                    bank_name: $('#bank-name').val(),
                    account_name: $('#account_name').val(),
                    account_number: $('#account-number').val(),
                    branch_name: $('#branch-name').val(),
                    mobile_provider: $('#mobile-provider').val(),
                    mobile_number: $('#mobile-number').val(),
                    facebook_link: $('#facebook-link').val(),
                    instagram_link: $('#instagram-link').val(),
                    twitter_link: $('#twitter-link').val(),
                    linkedin_link: $('#linkedin-link').val(),
                    website_link: $('#website-link').val(),
                    promotion_method: $('#promotion-method').val(),
                    nominee_name: $('#nominee-name').val(),
                    nominee_phone: $('#nominee-phone').val(),
                    nominee_relation: $('#nominee-relation').val(),
                };


                // AJAX POST request
                $.ajax({
                    url: "{{route('affiliator.store')}}", // Your route here
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    data: formData,
                    success: function(response) {


                        if (response.status === 201) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Congratulation!',
                                text: 'Registration successful!',
                            });
                            $('form')[0].reset();
                        }


                        if (response.status === 500) {

                            Swal.fire({
                                icon: 'error',
                                title: 'There was an server error!',
                                html: errorMessage,
                                confirmButtonColor: '#dc3545'
                            });
                        }



                        // alert('Registration successful!');
                        // Optionally, redirect or clear the form
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = '';

                            Object.keys(errors).forEach(function(key) {
                                errorMessage += `• ${errors[key][0]}<br>`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error!',
                                html: errorMessage,
                                confirmButtonColor: '#dc3545'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Email is already exist',
                            });
                        }
                    }

                });

            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>