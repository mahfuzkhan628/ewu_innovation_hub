/* =========================
   Page Load Smooth Effect
========================= */
window.addEventListener("load", () => {
    document.body.classList.add("loaded");
});

/* =========================
   Navbar Scroll Effect
========================= */
const navbar = document.querySelector(".navbar");
if (navbar) {
    navbar.style.background = "#ffffff";
    navbar.style.backgroundColor = "#ffffff";
    navbar.style.opacity = "1";
    navbar.style.backdropFilter = "none";
    navbar.style.webkitBackdropFilter = "none";
    navbar.style.boxShadow = "0 5px 20px rgba(0,0,0,0.08)";
    navbar.style.transition = "none";
}

/* =========================
   Hero Card 3D Tilt Effect
========================= */
const heroCard = document.querySelector(".hero-card");
if (heroCard) {
    heroCard.addEventListener("mousemove", (e) => {
        let rect = heroCard.getBoundingClientRect();
        let x = e.clientX - rect.left;
        let y = e.clientY - rect.top;

        let rotateX = ((y - rect.height / 2) / 20) * -1;
        let rotateY = (x - rect.width / 2) / 20;

        heroCard.style.transform = `
            perspective(1000px)
            rotateX(${rotateX}deg)
            rotateY(${rotateY}deg)
            scale(1.05)
        `;
    });

    heroCard.addEventListener("mouseleave", () => {
        heroCard.style.transform = `
            perspective(1000px)
            rotateX(0deg)
            rotateY(0deg)
            scale(1)
        `;
    });
}

/* =========================
   Feature Scroll Reveal
========================= */
const features = document.querySelectorAll(".feature-card");
function reveal() {
    features.forEach((card) => {
        let top = card.getBoundingClientRect().top;
        let height = window.innerHeight;

        if (top < height - 120) {
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }
    });
}
window.addEventListener("scroll", reveal);
reveal();

/* =========================
   Counter Animation
========================= */
const counters = document.querySelectorAll(".counter");
counters.forEach(counter => {
    let target = parseInt(counter.dataset.target);
    let count = 0;

    let timer = setInterval(() => {
        count += Math.ceil(target / 80);
        if (count >= target) {
            count = target;
            clearInterval(timer);
        }
        counter.innerText = count;
    }, 30);
});

/* =========================
   Button Ripple Effect
========================= */
const buttons = document.querySelectorAll(".btn");
buttons.forEach(button => {
    button.addEventListener("click", function (e) {
        let ripple = document.createElement("span");
        ripple.className = "ripple";
        let rect = this.getBoundingClientRect();
        ripple.style.left = `${e.clientX - rect.left}px`;
        ripple.style.top = `${e.clientY - rect.top}px`;

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

/* =========================
   Smooth Scroll
========================= */
document.querySelectorAll("a[href^='#']").forEach(link => {
    link.addEventListener("click", (e) => {
        const targetId = link.getAttribute("href");
        if (targetId !== "#") {
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({ behavior: "smooth" });
            }
        }
    });
});

/* =========================
   LOGIN CARD 3D EFFECT
========================= */
const authCard = document.querySelector(".auth-card");
if (authCard) {
    authCard.addEventListener("mousemove", (e) => {
        const rect = authCard.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        let rotateX = ((y - rect.height / 2) / 25) * -1;
        let rotateY = (x - rect.width / 2) / 25;

        authCard.style.transform = `
            perspective(1000px)
            rotateX(${rotateX}deg)
            rotateY(${rotateY}deg)
            scale(1.01)
        `;
    });

    authCard.addEventListener("mouseleave", () => {
        authCard.style.transform = `
            perspective(1000px)
            rotateX(0deg)
            rotateY(0deg)
            scale(1)
        `;
    });
}

/* =========================
   INPUT FOCUS ANIMATION
========================= */
const inputs = document.querySelectorAll(".input-group-custom input");
inputs.forEach(input => {
    input.addEventListener("focus", () => {
        if (input.parentElement) {
            input.parentElement.style.transform = "translateY(-3px)";
        }
    });

    input.addEventListener("blur", () => {
        if (input.parentElement) {
            input.parentElement.style.transform = "translateY(0)";
        }
    });
});

/* =========================
   PASSWORD SHOW / HIDE
========================= */
const toggleButtons = document.querySelectorAll(".toggle-password");
toggleButtons.forEach(button => {
    button.addEventListener("click", (e) => {
        e.preventDefault();
        const wrapper = button.closest(".password-wrapper") || button.parentElement;
        const input = wrapper.querySelector("input");

        if (input) {
            if (input.type === "password") {
                input.type = "text";
                button.textContent = "🙈";
            } else {
                input.type = "password";
                button.textContent = "👁️";
            }
        }
    });
});

/* =========================
   LOGIN VALIDATION
========================= */
const loginForm = document.getElementById("loginForm");
if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const message = document.getElementById("loginMessage");

        if (!emailInput || !passwordInput) return;

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        message.innerHTML = "";

        if (email === "") {
            e.preventDefault();
            message.innerHTML = `<div class="error-message" style="color: #ff4d4d; margin-bottom: 10px;">Please enter your email.</div>`;
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            e.preventDefault();
            message.innerHTML = `<div class="error-message" style="color: #ff4d4d; margin-bottom: 10px;">Please enter a valid email address.</div>`;
            return;
        }

        if (password === "") {
            e.preventDefault();
            message.innerHTML = `<div class="error-message" style="color: #ff4d4d; margin-bottom: 10px;">Please enter your password.</div>`;
            return;
        }

        if (password.length < 6) {
            e.preventDefault();
            message.innerHTML = `<div class="error-message" style="color: #ff4d4d; margin-bottom: 10px;">Password must be at least 6 characters.</div>`;
            return;
        }
    });
}

/* =========================
   REGISTER VALIDATION
========================= */
const registerForm = document.getElementById("registerForm");
if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
        const nameInput = document.querySelector('input[name="name"]');
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
        const message = document.getElementById("registerMessage");

        const name = nameInput ? nameInput.value.trim() : "";
        const email = emailInput ? emailInput.value.trim() : "";
        const password = passwordInput ? passwordInput.value.trim() : "";
        const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value.trim() : "";

        message.innerHTML = "";

        if (name === "") {
            e.preventDefault();
            message.innerHTML = `<div class="register-error" style="color: #ff4d4d; margin-bottom: 10px;">Please enter your name.</div>`;
            return;
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            e.preventDefault();
            message.innerHTML = `<div class="register-error" style="color: #ff4d4d; margin-bottom: 10px;">Enter a valid email.</div>`;
            return;
        }

        if (password.length < 6) {
            e.preventDefault();
            message.innerHTML = `<div class="register-error" style="color: #ff4d4d; margin-bottom: 10px;">Password must be at least 6 characters.</div>`;
            return;
        }

        if (password !== confirmPassword) {
            e.preventDefault();
            message.innerHTML = `<div class="register-error" style="color: #ff4d4d; margin-bottom: 10px;">Passwords do not match.</div>`;
            return;
        }
    });
}