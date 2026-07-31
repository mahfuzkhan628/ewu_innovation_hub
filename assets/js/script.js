
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


window.addEventListener("scroll", () => {


    if(window.scrollY > 50){

        navbar.style.boxShadow =
        "0 10px 30px rgba(0,0,0,0.15)";

        navbar.style.transition =
        "0.3s";

    }

    else{

        navbar.style.boxShadow =
        "0 5px 20px rgba(0,0,0,0.08)";

    }


});







/* =========================
   Hero Card 3D Tilt Effect
========================= */


const heroCard =
document.querySelector(".hero-card");


if(heroCard){


heroCard.addEventListener("mousemove",(e)=>{


let rect =
heroCard.getBoundingClientRect();


let x =
e.clientX - rect.left;


let y =
e.clientY - rect.top;



let rotateX =
((y - rect.height/2) / 20) * -1;


let rotateY =
(x - rect.width/2) / 20;



heroCard.style.transform =
`
perspective(1000px)
rotateX(${rotateX}deg)
rotateY(${rotateY}deg)
scale(1.05)
`;



});



heroCard.addEventListener("mouseleave",()=>{


heroCard.style.transform =
`
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


const features =
document.querySelectorAll(".feature-card");



function reveal(){


features.forEach((card)=>{


let top =
card.getBoundingClientRect().top;


let height =
window.innerHeight;



if(top < height - 120){


card.style.opacity = "1";

card.style.transform =
"translateY(0)";


}


});


}



window.addEventListener(
"scroll",
reveal
);


reveal();








/* =========================
   Counter Animation
========================= */


const counters =
document.querySelectorAll(".counter");



counters.forEach(counter=>{


let target =
parseInt(counter.dataset.target);



let count = 0;



let timer =
setInterval(()=>{


count += Math.ceil(target/80);



if(count >= target){

count = target;

clearInterval(timer);

}



counter.innerText = count;



},30);



});







/* =========================
   Button Ripple Effect
========================= */


const buttons =
document.querySelectorAll(".btn");



buttons.forEach(button=>{


button.addEventListener("click",function(e){



let ripple =
document.createElement("span");


ripple.className =
"ripple";


let rect =
this.getBoundingClientRect();


ripple.style.left =
`${e.clientX - rect.left}px`;


ripple.style.top =
`${e.clientY - rect.top}px`;



this.appendChild(ripple);



setTimeout(()=>{

ripple.remove();

},600);



});


});







/* =========================
   Smooth Scroll
========================= */


document.querySelectorAll("a[href^='#']")
.forEach(link=>{


link.addEventListener("click",(e)=>{


e.preventDefault();


document.querySelector(
link.getAttribute("href")
)
.scrollIntoView({

behavior:"smooth"

});


});


});

/* =========================
   LOGIN CARD 3D EFFECT
========================= */


const authCard = document.querySelector(".auth-card");


if(authCard){


authCard.addEventListener("mousemove",(e)=>{


    const rect = authCard.getBoundingClientRect();


    const x = e.clientX - rect.left;

    const y = rect.top;



});


authCard.addEventListener("mouseleave",()=>{


    authCard.style.transform =

    `

    perspective(1000px)

    rotateX(0deg)

    rotateY(0deg)

    scale(1)

    `;


});


}



authCard.addEventListener("mouseleave",()=>{


    authCard.style.transform =

    `

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


const inputs =
document.querySelectorAll(".input-group-custom input");



inputs.forEach(input=>{


input.addEventListener("focus",()=>{


    input.parentElement.style.transform =
    "translateY(-5px)";


});





input.addEventListener("blur",()=>{


    input.parentElement.style.transform =
    "translateY(0)";


});


});






/* =========================
   PASSWORD SHOW/HIDE
========================= */


const passwordInput =
document.querySelector(
'input[name="password"]'
);



if(passwordInput){


let toggle =
document.createElement("span");


toggle.innerHTML="👁";


toggle.style.cursor="pointer";

toggle.style.position="absolute";

toggle.style.right="20px";

toggle.style.marginTop="-43px";

toggle.style.color="#764ba2";




passwordInput.parentElement.style.position="relative";


passwordInput.parentElement.appendChild(toggle);





toggle.addEventListener("click",()=>{


if(passwordInput.type==="password"){


passwordInput.type="text";

toggle.innerHTML="🙈";


}

else{


passwordInput.type="password";

toggle.innerHTML="👁";


}



});


}






/* =========================
   LOGIN BUTTON CLICK EFFECT
========================= */


const loginBtn =
document.querySelector(".auth-btn");



if(loginBtn){


loginBtn.addEventListener("click",()=>{


loginBtn.innerHTML="Logging in...";


});


}




/* =========================
   LOGIN VALIDATION
========================= */


const loginForm =
document.getElementById("loginForm");


if(loginForm){


loginForm.addEventListener("submit",(e)=>{


const email =
document.querySelector(
'input[name="email"]'
).value.trim();



const password =
document.querySelector(
'input[name="password"]'
).value.trim();



const message =
document.getElementById("loginMessage");



message.innerHTML="";



if(email===""){


e.preventDefault();


message.innerHTML=

`
<div class="error-message">

Please enter your email.

</div>
`;

return;


}




const emailPattern =
/^[^\s@]+@[^\s@]+\.[^\s@]+$/;



if(!emailPattern.test(email)){


e.preventDefault();


message.innerHTML=

`
<div class="error-message">

Please enter a valid email address.

</div>
`;

return;


}





if(password===""){


e.preventDefault();


message.innerHTML=

`
<div class="error-message">

Please enter your password.

</div>
`;

return;


}




if(password.length < 6){


e.preventDefault();


message.innerHTML=

`
<div class="error-message">

Password must be at least 6 characters.

</div>
`;

return;


}




});



}


/* =========================
   REGISTER VALIDATION
========================= */


const registerForm =
document.getElementById("registerForm");



if(registerForm){



registerForm.addEventListener("submit",(e)=>{


const name =
document.querySelector('input[name="name"]').value.trim();



const email =
document.querySelector('input[name="email"]').value.trim();



const password =
document.querySelector('input[name="password"]').value.trim();



const confirmPassword =
document.querySelector('input[name="confirm_password"]').value.trim();



const message =
document.getElementById("registerMessage");



message.innerHTML="";





if(name===""){


e.preventDefault();


message.innerHTML=

`
<div class="register-error">

Please enter your name.

</div>
`;

return;

}





const emailPattern =
/^[^\s@]+@[^\s@]+\.[^\s@]+$/;



if(!emailPattern.test(email)){


e.preventDefault();


message.innerHTML=

`
<div class="register-error">

Enter a valid email.

</div>
`;

return;


}





if(password.length < 6){


e.preventDefault();


message.innerHTML=

`
<div class="register-error">

Password must be at least 6 characters.

</div>
`;

return;


}





if(password !== confirmPassword){


e.preventDefault();


message.innerHTML=

`
<div class="register-error">

Passwords do not match.

</div>
`;

return;


}



});



}

/* =========================
 PASSWORD SHOW HIDE
========================= */

document.querySelectorAll(".toggle-password").forEach(function(button){


    button.addEventListener("click", function(){


        let passwordInput = 
        this.parentElement.querySelector(".password-field");



        if(passwordInput.type === "password"){


            passwordInput.type = "text";

            this.innerHTML = "🙈";


        } 
        else {


            passwordInput.type = "password";

            this.innerHTML = "👁";


        }


    });


});