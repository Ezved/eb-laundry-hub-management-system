<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Order Summary</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background:#ffe5ff; }
    .modal-content{
      background:#ffe5ff;
      border-radius:20px;
      text-align:center;
      padding:30px 20px;
      position: relative;
    }
    .modal-content h2{
      font-weight:700;
      color:#173F7B;
    }
    .modal-content p{
      color:#555;
      margin-bottom:25px;
    }
    .btn-pink{
      background:#FFB5FE;
      color:#173F7B;
      font-weight:700;
      border-radius:999px;
      padding:10px 30px;
      border:none;
    }
    .btn-close{
      background:none;
      border:0;
      font-size:1.5rem;
      color:#173F7B;
      position:absolute;
      top:15px;
      right:15px;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <!-- Example Confirm button -->
  <button class="btn btn-primary" id="confirmBtn">Confirm</button>
</div>

<!-- Thank You Modal -->
<div class="modal fade" id="thankYouModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <h2>Order Received.<br>Thank You For Choosing Us!</h2>
      <p>An order receipt has been sent to your email.</p>
      <a href="{{ route('user.dashboard') }}" class="btn-pink">Back</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('confirmBtn').addEventListener('click', function(){
    var myModal = new bootstrap.Modal(document.getElementById('thankYouModal'));
    myModal.show();
  });
</script>
</body>
</html>
