<?php include('include/currentPage_header.php') ?>
<section id="contact ">
<div class="container-xl mb-5 p-5">
	<div class="row">
		<div class="col-md-8 mx-auto">
		<div class="message"></div>
			<div class="contact-form">
				<h1>Contact Us</h1>
				<p class="hint-text">We'd love to hear from you, please drop us a line if you've any query.</p>
				<form id="contact-form" action="functions.php" method="post"  autocomplete="off">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="inputFirstName">First Name</label>
								<input type="text" class="form-control" id="FirstName" name="FirstName" required>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="inputLastName">Last Name</label>
								<input type="text" class="form-control" id="LastName" name="LastName" required>
							</div>
						</div>
					</div>            
					<div class="form-group">
						<label for="inputEmail">Email Address</label>
						<input type="email" class="form-control" id="Email" name="Email" required>
					</div>
					<div class="form-group">
						<label for="inputMessage">Message</label>
						<textarea class="form-control" id="Message" name="Message" rows="5" required></textarea>
					</div>
					<button type="submit" class="btn btn-primary" name = "contact" > Submit </button>
				</form>
			</div>
		</div>
	</div>
</section>

<script>
$(document).ready(function() {
  $('#contact-form').on('submit', function(e) {
    e.preventDefault();

    // Simple client-side validation
    const firstName = $('#FirstName').val().trim();
    const lastName = $('#LastName').val().trim();
    const email = $('#Email').val().trim();
    const message = $('#Message').val().trim();

    const namePattern = /^[A-Za-z]+$/;

    if (!firstName || !lastName || !email || !message) {
      $('.message').html(`<div class="alert alert-danger">All fields are required.</div>`);
      return;
    }
    if (!namePattern.test(firstName)) {
      $('.message').html(`<div class="alert alert-danger">First Name must contain only letters.</div>`);
      return;
    }
    if (!namePattern.test(lastName)) {
      $('.message').html(`<div class="alert alert-danger">Last Name must contain only letters.</div>`);
      return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
      $('.message').html(`<div class="alert alert-danger">Invalid email address.</div>`);
      return;
    }

    if (message.length < 10) {
      $('.message').html(`<div class="alert alert-danger">Message should be at least 10 characters.</div>`);
      return;
    }

    // AJAX request if validation passes
    const formData = new FormData(this);

    $.ajax({
      url: "functions.php",
      type: "POST",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      success: function(data) {
        const json = JSON.parse(data);
        if (json.error) {
          $('.message').html(`<div class="alert alert-danger">${json.error}</div>`);
        } else {
          $('.message').html(`<div class="alert alert-success">${json.msg}</div>`);
          $('#contact-form')[0].reset();
        }
      },
      error: function() {
        $('.message').html(`<div class="alert alert-danger">Something went wrong. Please try again.</div>`);
      }
    });
  });
});
</script>

<?php include('include/footer.php')?>