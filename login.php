<?php

include "includes/header.php";
include "includes/navbar.php";

?>


<section class="auth-section">


    <div class="container">


        <div class="row align-items-center min-vh-100">


            <!-- Left Content -->

            <div class="col-lg-6 auth-left">


                <h1>
                    Welcome Back To
                    <span>
                        EWU Innovation Hub
                    </span>
                </h1>


                <p>

                    Login to explore innovative ideas, connect with faculty mentors,
                    and continue your innovation journey.

                </p>



                <div class="auth-points">


                    <div>
                        💡 Share Innovative Ideas
                    </div>


                    <div>
                        👨‍🏫 Connect With Faculty Mentors
                    </div>


                    <div>
                        🚀 Build Future Solutions
                    </div>


                </div>



            </div>





            <!-- Login Card -->


            <div class="col-lg-5 offset-lg-1">


                <div class="auth-card">


                    <h2>
                        Login
                    </h2>


                    <p class="auth-subtitle">
                        Access your account
                    </p>




                    <form
                        id="loginForm"
                        action="auth/login_process.php"
                        method="POST">


                        <div class="input-group-custom">


                            <label>
                                Email Address
                            </label>


                            <input
                                type="email"
                                name="email"
                                placeholder="Enter your email"
                                required>


                        </div>





                        <div class="input-group-custom">


                            <label>
                                Password
                            </label>


                            <div class="password-wrapper">

                                <input
                                    class="password-field"
                                    type="password"
                                    name="password"
                                    placeholder="Enter your password">


                                <button
                                    type="button"
                                    class="toggle-password">
                                    👁
                                </button>

                            </div>



                        </div>





                        <div class="remember">


                            <div>

                                <input
                                    type="checkbox"
                                    name="remember">

                                Remember me

                            </div>


                            <a href="#">
                                Forgot Password?
                            </a>


                        </div>




                        <div id="loginMessage"></div>

                        <button
                            type="submit"
                            class="auth-btn">

                            Login

                        </button>




                        <p class="register-text">

                            Don't have an account?

                            <a href="register.php">
                                Create Account
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