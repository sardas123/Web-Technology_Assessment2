<?php
// ================= PHP (server side) =================
// If the form is posted via AJAX, validate and return JSON; otherwise show the page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    function clean($v) { return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8'); }

    $fullname  = clean($_POST['fullname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $dob       = clean($_POST['dob'] ?? '');
    $gender    = clean($_POST['gender'] ?? '');
    $course    = clean($_POST['course'] ?? '');
    $address   = clean($_POST['address'] ?? '');
    $interests = isset($_POST['interests']) ? array_map('clean', $_POST['interests']) : [];

    $errors = [];
    if (strlen($fullname) < 3)                      $errors[] = 'Name is too short.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
    if (!preg_match('/^[6-9][0-9]{9}$/', $phone))   $errors[] = 'Invalid phone number.';
    if ($dob === '')                                $errors[] = 'Date of birth is required.';
    if ($gender === '')                             $errors[] = 'Gender is required.';
    if ($course === '')                             $errors[] = 'Course is required.';
    if (strlen($address) < 10)                      $errors[] = 'Address is too short.';

    if ($errors) {
        echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
        exit;
    }

    $email = clean($email);
    $phone = clean($phone);
    $interestText = $interests ? implode(', ', $interests) : 'None selected';
    $regId   = 'REG' . date('Ymd') . rand(100, 999);
    $regDate = date('d M Y, h:i A');

    $html  = '<div class="success-banner">Registration successful! Your ID is ' . $regId . '</div>';
    $html .= '<table class="details">';
    $html .= "<tr><th>Registration ID</th><td>$regId</td></tr>";
    $html .= "<tr><th>Full name</th><td>$fullname</td></tr>";
    $html .= "<tr><th>Email</th><td>$email</td></tr>";
    $html .= "<tr><th>Phone</th><td>$phone</td></tr>";
    $html .= "<tr><th>Date of birth</th><td>$dob</td></tr>";
    $html .= "<tr><th>Gender</th><td>$gender</td></tr>";
    $html .= "<tr><th>Course</th><td>$course</td></tr>";
    $html .= "<tr><th>Interests</th><td>$interestText</td></tr>";
    $html .= "<tr><th>Address</th><td>" . nl2br($address) . "</td></tr>";
    $html .= "<tr><th>Registered on</th><td>$regDate</td></tr>";
    $html .= '</table>';
    $html .= '<button type="button" id="printBtn" class="print-btn">Print</button>';

    echo json_encode(['status' => 'success', 'html' => $html]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Online Registration Form</title>

  <!-- ================= CSS ================= -->
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: "Segoe UI", Tahoma, Arial, sans-serif; background: #eef2f7; color: #1f2937; padding: 24px 12px; }
    .container { max-width: 760px; margin: 0 auto; background: #fff; padding: 32px; border-radius: 10px; border-top: 6px solid #1e4e8c; box-shadow: 0 4px 16px rgba(0,0,0,.08); }
    h1 { margin: 0 0 4px; color: #1e4e8c; }
    .subtitle { margin: 0 0 24px; color: #6b7280; }
    .row { display: flex; gap: 20px; flex-wrap: wrap; }
    .row .field { flex: 1 1 260px; }
    .field { margin-bottom: 18px; }
    label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 14px; }
    input[type=text], input[type=email], input[type=date], select, textarea {
      width: 100%; padding: 10px 12px; border: 1px solid #c5ccd6; border-radius: 6px; font-size: 15px; font-family: inherit;
    }
    input:focus, select:focus, textarea:focus { outline: 2px solid #1e4e8c; border-color: #1e4e8c; }
    .inline { display: flex; gap: 16px; flex-wrap: wrap; }
    .inline label { font-weight: 400; display: flex; align-items: center; gap: 6px; margin: 0; }
    .error { display: block; color: #b91c1c; font-size: 13px; min-height: 16px; margin-top: 4px; }
    .invalid { border-color: #b91c1c !important; }
    .actions { display: flex; gap: 12px; }
    button { padding: 11px 26px; border: none; border-radius: 6px; background: #1e4e8c; color: #fff; font-size: 15px; cursor: pointer; }
    button:hover { background: #163b6b; }
    button:disabled { background: #94a3b8; cursor: wait; }
    button.secondary { background: #e5e7eb; color: #1f2937; }
    button.secondary:hover { background: #d1d5db; }

    #result { margin-top: 28px; }
    .success-banner { background: #e7f6ec; color: #166534; border-left: 5px solid #16a34a; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; font-weight: 600; }
    .fail-banner { background: #fdecec; color: #991b1b; border-left: 5px solid #dc2626; padding: 12px 16px; border-radius: 6px; }
    table.details { width: 100%; border-collapse: collapse; }
    table.details th, table.details td { text-align: left; padding: 10px 12px; border: 1px solid #dbe1ea; }
    table.details th { background: #f1f5fb; width: 35%; color: #1e4e8c; }
    .print-btn { margin-top: 16px; }

    @media print {
      body { background: #fff; padding: 0; }
      form, h1, .subtitle, .print-btn { display: none; }
      .container { box-shadow: none; border: none; }
    }
  </style>
</head>
<body>
  <!-- ================= HTML ================= -->
  <div class="container">
    <h1>Student Registration</h1>
    <p class="subtitle">Fill in your details and press Register.</p>

    <form id="regForm" novalidate>
      <div class="row">
        <div class="field">
          <label for="fullname">Full name</label>
          <input type="text" id="fullname" name="fullname" placeholder="e.g. Ananya Rao">
          <span class="error"></span>
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="you@example.com">
          <span class="error"></span>
        </div>
      </div>

      <div class="row">
        <div class="field">
          <label for="phone">Phone (10 digits)</label>
          <input type="text" id="phone" name="phone" maxlength="10" placeholder="9876543210">
          <span class="error"></span>
        </div>
        <div class="field">
          <label for="dob">Date of birth</label>
          <input type="date" id="dob" name="dob">
          <span class="error"></span>
        </div>
      </div>

      <div class="row">
        <div class="field">
          <label>Gender</label>
          <div class="inline">
            <label><input type="radio" name="gender" value="Female"> Female</label>
            <label><input type="radio" name="gender" value="Male"> Male</label>
            <label><input type="radio" name="gender" value="Other"> Other</label>
          </div>
          <span class="error"></span>
        </div>
        <div class="field">
          <label for="course">Course</label>
          <select id="course" name="course">
            <option value="">-- Select course --</option>
            <option>B.Sc Computer Science</option>
            <option>B.Tech Information Technology</option>
            <option>BCA</option>
            <option>MCA</option>
            <option>B.Com</option>
          </select>
          <span class="error"></span>
        </div>
      </div>

      <div class="field">
        <label>Interests</label>
        <div class="inline">
          <label><input type="checkbox" name="interests[]" value="Coding"> Coding</label>
          <label><input type="checkbox" name="interests[]" value="Sports"> Sports</label>
          <label><input type="checkbox" name="interests[]" value="Music"> Music</label>
          <label><input type="checkbox" name="interests[]" value="Reading"> Reading</label>
        </div>
      </div>

      <div class="field">
        <label for="address">Address</label>
        <textarea id="address" name="address" rows="3" placeholder="House no, street, city, PIN"></textarea>
        <span class="error"></span>
      </div>

      <div class="field">
        <label class="inline"><input type="checkbox" id="agree" name="agree"> I confirm the details above are correct</label>
        <span class="error"></span>
      </div>

      <div class="actions">
        <button type="submit" id="submitBtn">Register</button>
        <button type="reset" class="secondary">Clear</button>
      </div>
    </form>

    <div id="result"></div>
  </div>

  <!-- ================= jQuery + JavaScript ================= -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
  $(document).ready(function () {

    function showError(input, msg) {
      $(input).addClass("invalid");
      $(input).closest(".field").find(".error").first().text(msg);
    }
    function clearErrors() { $(".invalid").removeClass("invalid"); $(".error").text(""); }

    function validate() {
      clearErrors();
      var ok = true;
      var name = $.trim($("#fullname").val());
      if (name.length < 3 || !/^[A-Za-z .]+$/.test(name)) { showError("#fullname", "Enter your full name (letters only, min 3)."); ok = false; }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($.trim($("#email").val()))) { showError("#email", "Enter a valid email address."); ok = false; }
      if (!/^[6-9][0-9]{9}$/.test($("#phone").val())) { showError("#phone", "Enter a valid 10-digit mobile number."); ok = false; }
      if (!$("#dob").val()) { showError("#dob", "Select your date of birth."); ok = false; }
      if (!$("input[name=gender]:checked").length) { showError("input[name=gender]", "Select a gender."); ok = false; }
      if (!$("#course").val()) { showError("#course", "Select a course."); ok = false; }
      if ($.trim($("#address").val()).length < 10) { showError("#address", "Address must be at least 10 characters."); ok = false; }
      if (!$("#agree").is(":checked")) { showError("#agree", "Please confirm the details."); ok = false; }
      return ok;
    }

    $("#regForm").on("submit", function (e) {
      e.preventDefault();
      if (!validate()) return;
      $("#submitBtn").prop("disabled", true).text("Submitting...");

      $.ajax({
        url: "",                       // posts to this same file
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (res) {
          if (res.status === "success") {
            $("#result").html(res.html);
            $("#regForm")[0].reset();
            $("html, body").animate({ scrollTop: $("#result").offset().top - 20 }, 400);
          } else {
            $("#result").html('<div class="fail-banner">' + res.message + "</div>");
          }
        },
        error: function () {
          $("#result").html('<div class="fail-banner">Could not reach the server. Is PHP running?</div>');
        },
        complete: function () { $("#submitBtn").prop("disabled", false).text("Register"); }
      });
    });

    $("#result").on("click", "#printBtn", function () { window.print(); });
    $("#phone").on("input", function () { this.value = this.value.replace(/\D/g, ""); });
  });
  </script>
</body>
</html>
