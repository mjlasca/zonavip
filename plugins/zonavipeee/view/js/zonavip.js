
var arreglomsg = [];
if(localStorage.getItem("chatbot")){
    arreglomsg = JSON.parse(localStorage.getItem("chatbot"));
    for(let i in arreglomsg){
      if(i%2 == 0){
        nuevomensaje(arreglomsg[i]);
      }else{
        nuevomensajebot(arreglomsg[i],false);
      }
      
    }
}

function nuevomensaje(message = ""){
  
    let msg = document.getElementById("msgbot").value;

    if(message != "")
      msg = message;

    if(msg != ""){
      
      var msgbots = document.getElementById("msgbots");
      if(message == ""){
        arreglomsg.push(msg);
        savelocalmsg(arreglomsg);
        nuevomensajeserv(msg);
      }
      document.getElementById("msgbot").setAttribute("readonly", true);

      var html = document.createElement('div'); // is a node
      html.className = "mensajes__user";
      html.innerHTML = '<div class="mensajes__user-msg">'+msg+'</div>';
      msgbots.appendChild(html);
      document.getElementById("msgbot").value = "";
      
    }

}


function nuevomensajebot(msg, savelocal = true){

  if(msg != ""){
    
    var msgbots = document.getElementById("msgbots");
    if(savelocal){
      arreglomsg.push(msg);
      savelocalmsg(arreglomsg);
    }
    
    document.getElementById("msgbot").removeAttribute("readonly");

    var html = document.createElement('div'); // is a node
    html.className = "mensajes__bot";
    html.innerHTML = msg;
    msgbots.appendChild(html);
    document.getElementById("msgbot").value = "";
    document.getElementById("msgbot").focus();
    scrolltop_();
  }

}


function scrolltop_()
{
    //Llevo el scroll al fondo
    var objDiv = document.getElementById("idchat");
    //console.log("--> "+objDiv.scrollHeight);
    objDiv.scrollTop = 600;
}

async function nuevomensajeserv1(msg) {
  return new Promise(function(resolve) {
      $.getJSON(url, 'nuevomsg='+msg, function (json) {
        console.log(JSON.stringify( json ));
          resolve(
              JSON.parse( JSON.stringify( json ) )
          );
      });
  })
}

async function nuevomensajeserv(msg){

  await nuevomensajeserv1(msg).then(res =>{
    nuevomensajebot(res.resp)
  }).catch(error => {
    console.error("Error al enviar el mensaje");
  });

}


function savelocalmsg(armsg){
  localStorage.setItem("chatbot", JSON.stringify(armsg));
}