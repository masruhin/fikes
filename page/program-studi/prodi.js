document.addEventListener('DOMContentLoaded',function(){
 const toggle=document.getElementById('menuToggle');
 const nav=document.getElementById('mainNav');
 if(toggle&&nav){toggle.addEventListener('click',()=>nav.classList.toggle('open'));}
 document.querySelectorAll('.dropdown-btn').forEach(btn=>{
   btn.addEventListener('click',function(e){
     if(window.innerWidth<=760){
       e.preventDefault();
       this.closest('.has-dropdown').classList.toggle('open');
     }
   });
 });
});