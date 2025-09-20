<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white text-center fw-bold">
                        Account Status
                    </div>
                    <div class="card-body text-center">
                        <h5 class="text-success mb-3">✅ Account Created Successfully</h5>
                        <p class="mb-4">Your account is awaiting admin approval and verification.<br>You'll be notified once access is granted.</p>

                        <div class="d-grid gap-3">
                           
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    🔓 Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-center small">
                        If you believe this is an error, please contact your administrator.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
l>
