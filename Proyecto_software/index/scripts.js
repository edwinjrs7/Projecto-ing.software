// ScrollTransition
window.addEventListener("scroll",function(){
    var header = document.querySelector("header");
    header.classList.toggle("abajo",window.scrollY > 0);
})

//ScrollReveal
ScrollReveal({
    reset: true,
    distance: '80px',
    duration: 2000,
    delay: 200
});

ScrollReveal().reveal('.contenedor-infor',{origin:'left'});
ScrollReveal().reveal('.contenedor-infor2',{origin:'right'});


