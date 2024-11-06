const menubar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menubar.addEventListener('click', function () {
    sidebar.classList.toggle('hide');
})

if(window.innerWidht < 768){
    sidebar.classList.add('hide');
}