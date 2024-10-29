$(function() { //ready function
  
 
   
  
   
  
 $('#login').on('click', function () {
    document.querySelector('dialog').showModal();
    document.querySelector('dialog').classList.add('open');
 }); 
    
$('#close-modal').on('click', function (e) {
   e.preventDefault()
   document.querySelector('dialog').classList.remove('open');
   setTimeout(()=>document.querySelector('dialog').close(),700)
   
});  
   

   
    


  });