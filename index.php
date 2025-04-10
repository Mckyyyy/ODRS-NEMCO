<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Request System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #80deea);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #ffffff;
            padding: 50px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            text-align: center;
            position: relative;
            max-width: 480px;
            width: 100%;
            transition: transform 0.3s;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        .new-request {
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .new-request button {
            background-color: #3f51b5;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .new-request button:hover {
            background-color: #303f9f;
        }

        .new-request .fa {
            margin-right: 6px;
        }

        .form-control {
            border-radius: 50px;
            font-size: 17px;
            padding: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .btn-track {
            width: 100%;
            border-radius: 50px;
            padding: 12px;
            font-size: 18px;
            margin-top: 15px;
            background-color: #00acc1;
            color: white;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-track:hover {
            background-color: #00838f;
        }

        h1 {
            font-size: 36px;
            color: #00acc1;
            font-weight: 600;
            margin-bottom: 30px;
        }

        img {
            width: 120px;
            margin-bottom: 20px;
        }

        .total-price-box {
            border: 2px solid #00acc1;
            padding: 15px;
            margin-top: 20px;
            font-size: 18px;
            font-weight: 500;
            color: #004d40;
            background-color: #e0f7fa;
            border-radius: 10px;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        ul.no-bullets {
            list-style-type: none;
            padding-left: 0;
        }

        .input-group-text {
            background-color: #00acc1;
            color: white;
            border-radius: 50px 0 0 50px;
            border: none;
        }

        .input-group .fa {
            font-size: 20px;
            padding: 0 10px;
        }

        /* Animation keyframe for rotating */
        @keyframes rotateIcon {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .new-request button:hover .fa {
            animation: rotateIcon 0.6s ease-in-out;
        }

        /* Keyframe for a subtle rotation animation */
        @keyframes subtleRotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(20deg);
            }
        }

        /* Apply the subtle rotation on input-group hover */
        .input-group:hover .fa-barcode {
            animation: subtleRotate 0.5s ease-in-out forwards;
        }

        /* Modal Container Styling */
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            text-align: center;
            /* Centering all modal content */
        }

        /* Modal Header Styling */
        .modal-header {
            background-color: #00acc1;
            color: white;
            border-bottom: none;
            padding: 10px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        /* Centering the Modal Title */
        .modal-title {
            flex: 1;
            text-align: center;
        }

        /* Modal Body Styling */
        .modal-body {
            font-size: 17px;
            padding: 30px 20px;
            background-color: #f1f9fb;
            line-height: 1.6;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        /* Modal Footer Styling */
        .modal-footer {
            display: flex;
            justify-content: center;
            border-top: none;
            padding: 15px;
            background-color: #e0f7fa;
        }

        /* Footer Button Styling */
        .modal-footer .btn-secondary {
            background-color: #00838f;
            border-radius: 30px;
            padding: 10px 30px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        /* Button Hover Effect */
        .modal-footer .btn-secondary:hover {
            background-color: #005662;
        }

        /* General Enhancements */
        h4 {
            margin-bottom: 15px;
            color: #006064;
        }

        ul {
            padding: 0;
            margin: 10px 0;
            text-align: left;
            /* Aligns lists to make them more readable */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="new-request">
            <button onclick="window.location.href='index_request.php'">
                <i class="fa fa-plus"></i>
                New Request
            </button>
        </div>
        <a href="https://www.facebook.com/nemco1946/" target="_blank">
            <img src="nemco.jpg" alt="Logo">
        </a>
        <h1>Track Your Request</h1>
        <form id="trackingForm" method="GET">
            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fa fa-barcode"></i>
                </span>
                <input type="text" class="form-control" placeholder="Enter Transaction Code" id="transactionCode"
                    name="transaction_code" required>
            </div>
            <button type="submit" class="btn btn-track">Track</button>
        </form>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="requestDetailsModal" tabindex="-1" aria-labelledby="requestDetailsLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white justify-content-center">
                    <h5 class="modal-title" id="requestDetailsLabel">Request Details</h5>
                </div>
                <div class="modal-body" id="modalBodyContent">
                    <!-- Request details will be dynamically inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingSpinner" style="display: none; text-align: center;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <script>
        // Highlighting transaction input
        const transactionInput = document.getElementById('transactionCode');

        transactionInput.addEventListener('input', () => {
            const isValid = /^[a-zA-Z0-9]{8,12}$/.test(transactionInput.value);
            if (isValid) {
                transactionInput.style.borderColor = 'green';
            } else {
                transactionInput.style.borderColor = 'red';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('trackingForm');
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const transactionCode = document.getElementById('transactionCode').value;

                $.ajax({
                    url: 'get_request_details.php',
                    method: 'GET',
                    data: { transaction_code: transactionCode },
                    dataType: 'json',
                    success: function (data) {
                        if (data.error) {
                            // Show error in modal
                            $('#requestDetailsLabel').text('Error');
                            $('#modalBodyContent').html(`<p>${data.error}</p>`);
                            $('#requestDetailsModal').modal('show');
                        } else {
                            let detailsHTML = '';
                            let totalPrice = 0;
                            let isExpired = false;

                            // Check for expiration
                            if (data.expiration_date) {
                                const expirationDate = new Date(data.expiration_date);
                                const currentDate = new Date();

                                // If the current date is past the expiration date
                                if (currentDate > expirationDate) {
                                    isExpired = true;
                                }
                            }

                            // Show message if expired
                            if (isExpired) {
                                detailsHTML = `
                                <div style="background-color: #f8d7da; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb; margin-top: 20px;">
                                    <h4 style="color: #721c24; font-size: 24px; font-weight: bold; text-align: center;">
                                        <i class="fas fa-exclamation-circle" style="color: #721c24; margin-right: 10px;"></i>Expired
                                    </h4>
                                    <p style="color: #721c24; font-size: 16px; text-align: center; font-weight: 500;">
                                        This transaction code has expired as the payment for your request was not settled before the expiration date.
                                    </p>
                                    <p style="color: #155724; font-size: 18px; font-weight: bold; text-align: center; margin-top: 10px;">
                                        Please make another request and follow the instructions to avoid expiration.
                                    </p>
                                    <p style="color: #721c24; font-size: 16px; text-align: center; font-weight: bold; margin-top: 10px;">
                                        Thank you for your understanding!
                                    </p>
                                </div>
                            `;

                            } else {
                                // Transcript of Records Section
                                let transcriptHTML = '<h4>Transcript of Records (ToR)</h4><ul>';

                                // Check each document type and the corresponding copies
                                if (data.forEnrollment === "1") {
                                    transcriptHTML += `<li>• For Enrollment ✔️ </li>`;
                                    totalPrice += 75;
                                }
                                if (data.forBoardExam === "1") {
                                    transcriptHTML += `<li>• For Board Exam ✔️ </li>`;
                                    totalPrice += 75;
                                }
                                if (data.forReference === "1") {
                                    transcriptHTML += `<li>• For Reference ✔️ </li>`;
                                    totalPrice += 75;
                                }
                                if (data.forStudiesAbroad === "1") {
                                    transcriptHTML += `<li>• For Studies Abroad ✔️ </li>`;
                                    totalPrice += 75;
                                }
                                if (data.forTransfer === "1") {
                                    transcriptHTML += `<li>• For Transfer ✔️ </li>`;
                                    totalPrice += 75;
                                }
                                if (data.forEmployment === "1") {
                                    transcriptHTML += `<li>• For Employment ✔️ </li>`;
                                    totalPrice += 75;
                                }
                                if (data.forRanking === "1") {
                                    transcriptHTML += `<li>• For Ranking and Promotion ✔️ </li>`;
                                    totalPrice += 75;
                                }

                                if (transcriptHTML !== '<h4>Transcript of Records (ToR)</h4><ul>') {
                                    transcriptHTML += '</ul>';
                                    detailsHTML += transcriptHTML;
                                }

                                // Other sections (Certification, Authentication, and Certificate) would be handled similarly
                                let otherDocsHTML = '<h4>Other Documents Requested</h4><ul>';

                                if (data.forCertification === "1") {
                                    otherDocsHTML += `<li>• (CAV) ✔️ </li>`;
                                    totalPrice += 60;
                                }
                                if (data.forDismissal === "1") {
                                    otherDocsHTML += `<li>• Honorable Dismissal ✔️ </li>`;
                                    totalPrice += 60;
                                }
                                if (data.forAuthentication === "1") {
                                    otherDocsHTML += `<li>• Authentication Page ✔️ </li>`;
                                    totalPrice += 20;
                                }
                                if (data.forReissuance === "1") {
                                    otherDocsHTML += `<li>• Re-issuance of Diploma ✔️ </li>`;
                                    totalPrice += 200;
                                }
                                if (data.forOthers === "1") {
                                    const othersText = data.others ? ` (${data.others})` : '';
                                    otherDocsHTML += `<li>• Others ✔️${othersText}</li>`;
                                    totalPrice += 25;
                                }
                                if (otherDocsHTML !== '<h4>Other Documents Requested</h4><ul>') {
                                    otherDocsHTML += '</ul>';
                                    detailsHTML += otherDocsHTML;
                                }

                                // Certificate Section
                                let certificateHTML = '<h4>Certificate</h4><ul>';
                                if (data.forGoodmoral === "1") {
                                    certificateHTML += `<li>• Good Moral Character ✔️ </li>`;
                                    totalPrice += 30;
                                }
                                if (data.forGpa === "1") {
                                    certificateHTML += `<li>• Certificate of GPA ✔️ </li>`;
                                    totalPrice += 30;
                                }
                                if (data.forEarnedUnits === "1") {
                                    certificateHTML += `<li>• Certificate of Earned Units ✔️ </li>`;
                                    totalPrice += 30;
                                }
                                if (data.forEnrollmentcert === "1") {
                                    certificateHTML += `<li>• Certificate of Enrollment ✔️ </li>`;
                                    totalPrice += 30;
                                }
                                if (data.forGraduating === "1") {
                                    certificateHTML += `<li>• Certificate of Graduating ✔️ </li>`;
                                    totalPrice += 30;
                                }
                                if (data.forGraduation === "1") {
                                    certificateHTML += `<li>• Certificate of Graduation ✔️ </li>`;
                                    totalPrice += 30;
                                }
                                if (data.forSpec === "1") {
                                    certificateHTML += `<li>• Certificate for summer job ✔️ </li>`;
                                    totalPrice += 30;
                                }
                                if (data.formediumInstruction === "1") {
                                    certificateHTML += `<li>• Medium of Instruction (MOI) ✔️ </li>`;
                                    totalPrice += 30;
                                }
                                if (data.forRegistration === "1") {
                                    certificateHTML += `<li>• Certificate of Registration ✔️ </li>`;
                                    totalPrice += 30;
                                }
                                if (certificateHTML !== '<h4>Certificate</h4><ul>') {
                                    certificateHTML += '</ul>';
                                    detailsHTML += certificateHTML;
                                }

                                // Status Handling
                                let statusMessage = `<strong>Status:</strong> <span style="background-color: yellow; font-size: 16px; padding: 5px; font-weight: bold;">${data.status}</span>`;

                                if (data.status === 'Pending') {
                                    statusMessage = `<strong>Status:</strong> <span style="background-color: #ffcc00; font-size: 16px; padding: 5px; font-weight: bold;">Pending</span>`;
                                } else if (data.status === 'Completed') {
                                    statusMessage = `<strong>Status:</strong> <span style="background-color: #00ff00; font-size: 16px; padding: 5px; font-weight: bold;">Ready for Final Printing</span>`;

                                    if (data.confirmed_date) {
                                        statusMessage += ` on: ${data.confirmed_date}`;
                                    }
                                    if (data.claim_date) {
                                        statusMessage += `
        <div style="border: 1px solid black; padding: 5px; display: inline-block;">
            <strong>Claim Date:</strong> ${data.claim_date}
        </div>
    `;
                                    }

                                    // Add 'View Slip' button without the Download PDF option
                                    statusMessage += `
    <div style="margin-top: 10px;">
        <button id="viewSlipBtnCompleted" class="btn btn-sm btn-primary" 
            style="text-decoration: none; font-size: 12px; padding: 5px 10px;">
            View Slip
        </button>
    </div>
`;
                                }


                                // Handle 'Released' status
                                if (data.status === 'Released') {
                                    statusMessage += `
        <div style="border: 1px solid black; padding: 5px; display: inline-block;">
            <strong>Released Date:</strong> ${data.claim_date}
        </div>
    `;
                                }

                                // Show expiration date and 'View Slip' for 'Confirmed' status
                                if (data.status === 'Confirmed') {
                                    if (data.expiration_date) {
                                        statusMessage += `
                                    <div style="border: 1px solid black; padding: 5px; display: inline-block;">
                                        <strong>Expiration Date:</strong> ${data.expiration_date}
                                    </div>
                                    <div style="color: red; font-weight: normal; font-size: 12px; margin-top: 10px;">
                                    Proceed to the NEMCO cashier to settle your payment before the expiration date to avoid transaction expiration.                                   
                                     </div>
                                    <div style="margin-top: 10px; font-size: 14px; font-weight: bold;">
                                        Click "View Slip" for your proof of payment.
                                    </div>
                                `;
                                    }

                                    // Add 'View Slip' button for Confirmed status
                                    statusMessage += `
        <div style="margin-top: 10px;">
            <button id="viewSlipBtn" class="btn btn-sm btn-primary" 
                style="text-decoration: none; font-size: 12px; padding: 5px 10px;">
                View Slip
            </button>
        </div>
    `;
                                }

                                // Handle expired status (display expired message)
                                if (isExpired) {
                                    statusMessage = `<strong>Status:</strong> <span style="background-color: #ff3333; font-size: 16px; padding: 5px; font-weight: bold;">Expired</span>`;
                                }


                                // Append final message to detailsHTML
                                detailsHTML += `<p>${statusMessage}</p>`;

                                // Total Price Section (showing the updated payment)
                                // Total Price Section (showing the updated payment)
                                if (data.payment && data.payment > 0) {
                                    // If status is 'Completed', display Final Price
                                    if (data.status === 'Completed') {
                                        detailsHTML += `
        <div class="total-price-box" style="padding: 12px 20px; max-width: 90%; margin: 10px auto; border-radius: 8px; border: 1px solid #00796b; background-color: #e0f2f1; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
            <strong style="color: #00796b; font-size: 20px; font-weight: bold;">Final Payment: ₱<span id="totalPrice">${data.payment}</span></strong>
        </div>
    <p style="margin-top: 10px; font-size: 14px; color: #004d40; text-align: center; font-weight: 500;">
            Please claim your requested documents on the provided claim date.
        </p>`;
                                    }

                                    // If status is not 'Completed', but 'Released', display Final Price
                                    else if (data.status === 'Released') {
                                        detailsHTML += `
            <div class="total-price-box" style="padding: 12px 20px; max-width: 90%; margin: 10px auto; border-radius: 8px; border: 1px solid #00796b; background-color: #e0f2f1; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                <strong style="color: #00796b; font-size: 20px; font-weight: bold;">Final Payment: ₱<span id="totalPrice">${data.payment}</span></strong>
            </div>`;
                                    }
                                    // If status is something else, display Tentative Payment
                                    else {
                                        detailsHTML += `
            <div class="total-price-box" style="padding: 12px 20px; max-width: 90%; margin: 10px auto; border-radius: 8px; border: 1px solid #00796b; background-color: #e0f2f1; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                <strong style="color: #00796b; font-size: 20px; font-weight: bold;">Tentative Payment: ₱<span id="totalPrice">${data.payment}</span></strong>
                <p style="color: #555; font-size: 14px; font-style: italic; margin-top: 5px;"><small><em>Please note: This is a tentative payment. The final payment may vary.</em></small></p>
            </div>`;
                                    }



                                }
                            }

                            if (detailsHTML === '') {
                                detailsHTML = `<p>No details available for this transaction.</p>`;
                            }

                            // Display the modal with the request details and total price
                            $('#modalBodyContent').html(detailsHTML);
                            $('#requestDetailsModal').modal('show');

                            // Add event listener to "View Slip" button
                            setTimeout(() => {
                                document.getElementById('viewSlipBtn')?.addEventListener('click', function () {
                                    viewSlip(data.transaction_code); // Fetch and display updated slip
                                });
                            }, 100);

                            setTimeout(() => {
                                document.getElementById('viewSlipBtnCompleted')?.addEventListener('click', function () {
                                    viewSlipCompleted(data.transaction_code); // Fetch and display updated slip without Download PDF
                                });
                            }, 100);

                        }
                    },
                    error: function () {
                        $('#loadingSpinner').hide();
                    }
                });
            });


        });

        function viewSlipCompleted(transactionCode) {
            $.ajax({
                url: 'fetch_request.php',
                method: 'GET',
                data: { transaction_code: transactionCode },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Start building the slip details with enhanced styling
                        let slipDetails = `
                        <div style="font-family: 'Arial', sans-serif; width: 100%; text-align: center; padding: 20px;">
                            <h2 style="color: #00796b; font-size: 24px; font-weight: bold; margin-bottom: 5px;">Final Copies</h2>
                            <p style="font-size: 14px; color: #555; margin-top: 0; padding-bottom: 20px;">Transaction Code: ${transactionCode}</p>
                            <p style="font-size: 16px; color: #ff0000; font-weight: bold; padding: 10px; background-color: #ffff00; border-radius: 5px;">
                                Note: This is your final copy of the documents.
                            </p>
                            <table style="width: 100%; margin-top: 30px; border-collapse: collapse; border: none;">
                                <thead style="background: linear-gradient(to right, #00796b, #004d40); color: white;">
                                    <tr>
                                        <th style="text-align: left; padding: 15px 20px; border: 1px solid #ddd; font-weight: bold; font-size: 16px;">Description</th>
                                        <th style="text-align: right; padding: 15px 20px; border: 1px solid #ddd; font-weight: bold; font-size: 16px;">Copies</th>
                                    </tr>
                                </thead>
                                <tbody>`;


                        // Key mapping for human-readable descriptions
                        const keyMapping = {
                            forEnrollment: "For Enrollment",
                            forBoardExam: "For Board Exam",
                            forReference: "For Reference",
                            forStudiesAbroad: "For Studies Abroad",
                            forTransfer: "For Transfer",
                            forEmployment: "For Employment",
                            forRanking: "For Ranking and Promotion",
                            forCertification: "CAV",
                            forDismissal: "Honorable Dismissal",
                            forAuthentication: "Authentication",
                            forReissuance: "Re-issuance of Diploma",
                            forOthers: "Others",
                            forGoodmoral: "Good Moral Character",
                            forGpa: "Certificate of GPA",
                            forEarnedUnits: "Certificate of Earned Units",
                            forEnrollmentcert: "Certificate of Enrollment",
                            forGraduating: "Certificate of Graduating",
                            forGraduation: "Certificate of Graduation",
                            forSpec: "Certificate for Summer Job",
                            formediumInstruction: "Medium of Instruction (MOI)",
                            forRegistration: "Certificate of Registration",
                        };

                        // Process keys to map to human-readable descriptions
                        Object.entries(response.copiesData).forEach(([key, value]) => {
                            let description = keyMapping[key] || key; // Use key mapping or fall back to original key
                            if (key === "forOthers" && response.copiesData.others) {
                                description += ` (${response.copiesData.others})`; // Add additional description for 'others'
                            }
                            slipDetails += `
                <tr>
                    <td style="padding: 15px 20px; text-align: left; border: 1px solid #ddd; font-size: 14px; color: #333;">${description}</td>
                    <td style="padding: 15px 20px; text-align: right; border: 1px solid #ddd; font-size: 14px; color: #333;">${value} copies</td>
                </tr>`;
                        });

                        // Close table and div
                        slipDetails += `
                    </tbody>
                </table>
            </div>`;

                        // Show the enhanced modal with adjusted width
                        Swal.fire({
                            html: slipDetails,
                            icon: 'info',
                            width: 750, // Wider modal for better layout
                            confirmButtonText: 'OK'
                        });

                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'Failed to fetch slip details.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while fetching the slip.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                }
            });
        }


        // Function to fetch and display the slip details
        function viewSlip(transactionCode) {
            $.ajax({
                url: 'fetch_request.php',
                method: 'GET',
                data: { transaction_code: transactionCode },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Start building the slip details with enhanced styling and additional note
                        // Start building the slip details with enhanced styling and more visible notes (only text)
                        let slipDetails = `
    <div style="font-family: 'Arial', sans-serif; width: 100%; text-align: center; padding: 20px;">
        <h2 style="color: #00796b; font-size: 24px; font-weight: bold; margin-bottom: 5px;">Slip For Payment</h2>
        <p style="font-size: 14px; color: #555; margin-top: 0; padding-bottom: 20px;">Transaction Code: ${transactionCode}</p>
        <p style="font-size: 16px; color: #d32f2f; font-weight: bold; margin-bottom: 20px;">
            Note: Please download the PDF and print the slip as proof.
        </p>
        <p style="font-size: 16px; color: #d32f2f; font-weight: bold; margin-bottom: 20px;">
            If you wish to update your document copies, please proceed to the registrar before settling your final payment.
        </p>
        <table style="width: 100%; margin-top: 30px; border-collapse: collapse; border: none;">
            <thead style="background: linear-gradient(to right, #00796b, #004d40); color: white;">
                <tr>
                    <th style="text-align: left; padding: 15px 20px; border: 1px solid #ddd; font-weight: bold; font-size: 16px;">Description</th>
                    <th style="text-align: right; padding: 15px 20px; border: 1px solid #ddd; font-weight: bold; font-size: 16px;">Copies</th>
                </tr>
            </thead>
            <tbody>`;


                        // Key mapping for human-readable descriptions
                        const keyMapping = {
                            forEnrollment: "For Enrollment",
                            forBoardExam: "For Board Exam",
                            forReference: "For Reference",
                            forStudiesAbroad: "For Studies Abroad",
                            forTransfer: "For Transfer",
                            forEmployment: "For Employment",
                            forRanking: "For Ranking and Promotion",
                            forCertification: "CAV",
                            forDismissal: "Honorable Dismissal",
                            forAuthentication: "Authentication",
                            forReissuance: "Re-issuance of Diploma",
                            forOthers: "Others",
                            forGoodmoral: "Good Moral Character",
                            forGpa: "Certificate of GPA",
                            forEarnedUnits: "Certificate of Earned Units",
                            forEnrollmentcert: "Certificate of Enrollment",
                            forGraduating: "Certificate of Graduating",
                            forGraduation: "Certificate of Graduation",
                            forSpec: "Certificate for Summer Job",
                            formediumInstruction: "Medium of Instruction (MOI)",
                            forRegistration: "Certificate of Registration",
                        };

                        // Process keys to map to human-readable descriptions
                        Object.entries(response.copiesData).forEach(([key, value]) => {
                            let description = keyMapping[key] || key; // Use key mapping or fall back to original key
                            if (key === "forOthers" && response.copiesData.others) {
                                description += ` (${response.copiesData.others})`; // Add additional description for 'others'
                            }
                            slipDetails += `
                    <tr>
                        <td style="padding: 15px 20px; text-align: left; border: 1px solid #ddd; font-size: 14px; color: #333;">${description}</td>
                        <td style="padding: 15px 20px; text-align: right; border: 1px solid #ddd; font-size: 14px; color: #333;">${value} copies</td>
                    </tr>`;
                        });

                        // Close table and div
                        slipDetails += `
                        </tbody>
                    </table>
                </div>`;

                        // Show the enhanced modal with adjusted width
                        Swal.fire({
                            html: slipDetails,
                            icon: 'info',
                            width: 750, // Wider modal for better layout
                            confirmButtonText: 'Download PDF',

                            preConfirm: () => {
                                // Use jsPDF to generate and download the PDF
                                const { jsPDF } = window.jspdf;
                                const doc = new jsPDF();

                                // Add a background color or watermark for a premium feel
                                doc.setFillColor(245, 245, 245); // Light gray
                                doc.rect(0, 0, 210, 297, 'F'); // Full-page background

                                // Add the company logo - perfectly centered
                                doc.addImage('nemco.jpg', 'JPEG', 90, 10, 30, 30);

                                // Add header details (Name, Address, Contact Info) - styled
                                doc.setFont("helvetica", "bold");
                                doc.setFontSize(18);
                                doc.setTextColor(0, 51, 153); // Dark blue for institution name
                                doc.text("Northeastern Mindanao Colleges", 105, 50, null, null, "center");

                                doc.setFontSize(12);
                                doc.setFont("helvetica", "normal");
                                doc.setTextColor(85, 85, 85); // Gray for address and contact info
                                doc.text("Rizal Street, Surigao City, 8400 Surigao del Norte", 105, 58, null, null, "center");
                                doc.text("Contact: (086) 826 1787", 105, 64, null, null, "center");

                                // Add a thicker decorative line below header
                                doc.setDrawColor(0, 51, 153); // Dark blue line
                                doc.setLineWidth(0.5); // Slightly thicker
                                doc.line(20, 70, 190, 70);

                                // Add title for Slip Details
                                doc.setFontSize(20);
                                doc.setFont("helvetica", "bold");
                                doc.setTextColor(34, 139, 34); // Green for title
                                doc.text("Slip Details", 105, 80, null, null, "center");

                                // Add transaction code section with styling
                                doc.setFontSize(14);
                                doc.setFont("helvetica", "normal");
                                doc.setTextColor(0, 0, 0); // Black text for transaction code
                                doc.text(`Transaction Code: ${transactionCode}`, 105, 90, null, null, "center");

                                // Add important note section
                                doc.setFontSize(12);
                                doc.setFont("helvetica", "italic");
                                doc.setTextColor(128, 0, 0); // Dark red for emphasis
                                const noteText = [
                                    "Note: Please proceed to the cashier and present this slip for your final payment.",
                                    "If you want to update your page copies, please proceed to the Registrar before paying."
                                ];
                                noteText.forEach((line, index) => {
                                    doc.text(line, 105, 100 + index * 5, { align: "center" });
                                });

                                // Add decorative borders to the table
                                let x = 20; // Left margin
                                let y = 110; // Starting Y position for table
                                let tableWidth = 170;

                                // Add table header with a decorative box
                                doc.setFillColor(0, 51, 153); // Dark blue header background
                                doc.rect(x, y, tableWidth, 12, 'F'); // Draw header rectangle

                                doc.setFontSize(12);
                                doc.setFont("helvetica", "bold");
                                doc.setTextColor(255, 255, 255); // White text for header
                                doc.text("Description", x + 10, y + 8);
                                doc.text("Copies", x + tableWidth - 10, y + 8, null, null, "right");

                                y += 14; // Move Y position for rows

                                // Add table rows with alternating row colors and a border
                                Object.entries(response.copiesData).forEach(([key, value], index) => {
                                    let description = keyMapping[key] || key;

                                    if (index % 2 === 0) {
                                        doc.setFillColor(240, 240, 240); // Light gray
                                    } else {
                                        doc.setFillColor(255, 255, 255); // White
                                    }
                                    doc.rect(x, y - 12, tableWidth, 12, 'F'); // Draw row background

                                    doc.setFontSize(11);
                                    doc.setFont("helvetica", "normal");
                                    doc.setTextColor(0, 0, 0); // Black text for rows
                                    doc.text(description, x + 10, y - 4);
                                    doc.text(`${value} copies`, x + tableWidth - 10, y - 4, null, null, "right");

                                    y += 14; // Move Y for the next row
                                });

                                // Add decorative elements below the table
                                doc.setDrawColor(0, 51, 153); // Dark blue
                                doc.line(20, y, 190, y); // Separator line below table

                                // Add signature line for Cashier Signature
                                const signatureY = y + 20; // Adjust Y position below the table and other text
                                doc.setDrawColor(0, 0, 0); // Black for signature line
                                doc.line(100, signatureY, 190, signatureY); // Draw line (X1, Y1, X2, Y2)
                                doc.setFontSize(12);
                                doc.setFont("helvetica", "italic");
                                doc.setTextColor(85, 85, 85); // Gray for label text
                                doc.text("Cashier Signature", 145, signatureY + 5, null, null, "center");

                                // Add final payment label after Cashier Signature
                                const finalPaymentY = signatureY + 20; // Position below the signature
                                doc.setFontSize(14);
                                doc.setFont("helvetica", "bold");
                                doc.setTextColor(0, 0, 0); // Black text
                                doc.text("Final Payment Amount: ______________________", 105, finalPaymentY, null, null, "center");

                                // Add footer with a small decorative bar
                                const footerY = doc.internal.pageSize.height - 20;

                                // Add "Thank you" message to the upper part of the footer
                                doc.setFontSize(12);
                                doc.setFont("helvetica", "italic");
                                doc.setTextColor(100); // Gray text
                                doc.text("Thank you for choosing Northeastern Mindanao Colleges!", 105, footerY - 15, null, null, "center");

                                // Draw the horizontal line
                                doc.setDrawColor(0, 0, 0); // Black line
                                doc.line(20, footerY - 5, 190, footerY - 5); // Horizontal line

                                // Add the generated by message and page number below the line
                                doc.setFontSize(12);
                                doc.setFont("helvetica", "italic");
                                doc.setTextColor(100); // Gray text
                                doc.text("Generated by NEMCO Registrar Documents Request System", 105, footerY + 5, null, null, "center");
                                // Save the PDF
                                doc.save('Slip_Details_Transaction.pdf');
                            }
                        });

                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'Failed to fetch slip details.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while fetching the slip.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                }
            });
        }
    </script>
    <script>
        // Function to get current Philippine time with seconds, AM/PM
        function getPhilippineTime() {
            // Create a new Date object and adjust it for GMT+8 (Philippine Standard Time)
            const now = new Date();
            const utcOffset = now.getTimezoneOffset() * 60000; // Time zone offset in milliseconds
            const philippineTime = new Date(now.getTime() + utcOffset + (3600000 * 8)); // GMT+8

            // Extract hours, minutes, seconds and determine AM/PM
            let hours = philippineTime.getHours();
            const minutes = philippineTime.getMinutes();
            const seconds = philippineTime.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // If hour is 0, set it to 12

            // Format minutes and seconds to be always two digits
            const minutesFormatted = minutes < 10 ? '0' + minutes : minutes;
            const secondsFormatted = seconds < 10 ? '0' + seconds : seconds;

            // Return the formatted time string with seconds
            return `${hours}:${minutesFormatted}:${secondsFormatted} ${ampm}`;
        }

        // Function to update the time in the popup every second
        function updateTimeInPopup() {
            const philippineTime = getPhilippineTime(); // Get current Philippine time
            document.getElementById('philippineTimeDisplay').innerText = philippineTime; // Update the displayed time
        }

        // Function to display the maintenance message popup with time
        function showMaintenancePopup() {
            Swal.fire({
                icon: 'info',
                html: `
                <h2>System Maintenance</h2>
                <p>The system is currently undergoing maintenance. Please check back later.</p>
                <p>The current Philippine time is:</p>
                <p id="philippineTimeDisplay" style="font-size: 1.5em;"></p>
            `,
                showConfirmButton: false, // Remove the OK button
                allowOutsideClick: false, // Prevent clicking outside to close
                allowEscapeKey: false, // Prevent Esc key to close
                allowEnterKey: false, // Prevent Enter key to close
                didOpen: () => {
                    // Start updating the time every second
                    updateTimeInPopup(); // Initial call to display time immediately
                    const timeInterval = setInterval(updateTimeInPopup, 1000); // Update every second

                    // Set interval to check for maintenance mode status
                    const intervalId = setInterval(() => {
                        if (localStorage.getItem('maintenanceMode') !== 'on') {
                            Swal.close(); // Close the popup when maintenance mode is turned off
                            clearInterval(intervalId); // Stop the maintenance check
                            clearInterval(timeInterval); // Stop the time updates
                        }
                    }, 1000); // Check every second
                }
            });
        }

        // Check if maintenance mode is already active on page load
        if (localStorage.getItem('maintenanceMode') === 'on') {
            showMaintenancePopup();
        }

        // Listen for changes to localStorage (maintenance mode toggles)
        window.addEventListener('storage', function (event) {
            if (event.key === 'maintenanceMode') {
                if (event.newValue === 'on') {
                    showMaintenancePopup(); // Show popup if maintenance mode is activated
                } else {
                    Swal.close(); // Close popup if maintenance mode is deactivated
                }
            }
        });


    </script>



</body>

</html>