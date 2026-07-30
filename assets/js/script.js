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