<?php

include "includes/header.php";
include "includes/navbar.php";

?>


<section class="auth-section">


<div class="container">


<div class="row align-items-center g-5">



<!-- =========================
     LEFT CONTENT
========================= -->


<div class="col-lg-6 auth-left">


<h1>

Join <span>EWU Innovation Hub</span>

</h1>



<p>

Create your account and become part of a platform
where students share innovative ideas and faculty
provide guidance and mentorship.

</p>




<div class="auth-points">


<div>
💡 Share your innovative ideas
</div>


<div>
👨‍🏫 Connect with faculty mentors
</div>


<div>
🚀 Turn ideas into real projects
</div>


</div>



</div>







<!-- =========================
     REGISTER CARD
========================= -->


<div class="col-lg-6">


<div class="auth-card register-card">



<h2>

Create Account

</h2>



<p class="auth-subtitle">

Register to join EWU Innovation Hub

</p>





<form 
id="registerForm"
action="auth/register_process.php"
method="POST">







<!-- NAME -->

<div class="input-group-custom">


<label>

Full Name

</label>



<input

type="text"

name="name"

placeholder="Enter your full name"

>



</div>







<!-- EMAIL -->

<div class="input-group-custom">


<label>

Email

</label>



<input

type="email"

name="email"

placeholder="Enter your email"

>



</div>







<!-- PASSWORD -->
<div class="input-group-custom">
    <label>Password</label>
    <div class="password-wrapper">
        <input class="password-field" type="password" name="password" placeholder="Create password">
        <button type="button" class="toggle-password">👁</button>
    </div>
</div>

<!-- CONFIRM PASSWORD -->
<div class="input-group-custom">
    <label>Confirm Password</label>
    <div class="password-wrapper">
        <input class="password-field" type="password" name="confirm_password" placeholder="Confirm password">
        <button type="button" class="toggle-password">👁</button>
    </div>
</div>





<!-- ROLE -->


<div class="input-group-custom">


<label>

Account Type

</label>



<select 
name="role"
class="register-select">


<option value="student">

Student

</option>



<option value="faculty">

Faculty

</option>


</select>



</div>








<!-- DEPARTMENT -->


<div class="input-group-custom">


<label>

Department

</label>



<input

type="text"

name="department"

placeholder="Example: CSE"

>



</div>







<!-- MESSAGE -->

<div id="registerMessage"></div>







<!-- BUTTON -->


<button

type="submit"

class="auth-btn">


Register


</button>







<p class="register-text">


Already have an account?


<a href="login.php">

Login

</a>



</p>




</form>




</div>


</div>



</div>


</div>


</section>





<?php

include "includes/footer.php";

?>