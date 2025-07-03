// LOGIN
console.clear();

const loginBtn = document.getElementById("loginForm");
const signupBtn = document.getElementById("signup");

loginBtn.addEventListener("click", (e) => {
  let parent = e.target.parentNode.parentNode;
  Array.from(parent.classList).find((element) => {
    if (element !== "slide-up") {
      parent.classList.add("slide-up");
    } else {
      signupBtn.parentNode.classList.add("slide-up");
      parent.classList.remove("slide-up");
    }
  });
});

signupBtn.addEventListener("click", (e) => {
  let parent = e.target.parentNode;
  Array.from(parent.classList).find((element) => {
    if (element !== "slide-up") {
      parent.classList.add("slide-up");
    } else {
      loginBtn.parentNode.parentNode.classList.add("slide-up");
      parent.classList.remove("slide-up");
    }
  });
});

// VARIABLES LOGIN
const email = document.querySelector("#email");
const password = document.querySelector("#password");
const btnLogin = document.querySelector("#btnLogin");

// VARIABLES REGISTER
const dniRegister = document.querySelector("#dniRegister");
const nombreRegister = document.querySelector("#nombreRegister");
const apellidoRegister = document.querySelector("#apellidoRegister");
const direccionRegister = document.querySelector("#direccionRegister");
const emailRegister = document.querySelector("#emailRegister");
const passwordRegister = document.querySelector("#passwordRegister");
const btnRegister = document.querySelector("#btnRegister");

document.addEventListener("DOMContentLoaded", function () {
  // LOGIN
  btnLogin.onclick = function (e) {
    e.preventDefault();
    if (email.value.trim() === "" || password.value.trim() === "") {
      alerta("INGRESA CORREO Y CONTRASEÑA", 2);
      return;
    }

    let data = new FormData();
    data.append("email", email.value.trim());
    data.append("clave", password.value.trim());

    const url = ruta + "profile/validar";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        const res = JSON.parse(this.responseText);
        if (res.icono === "success") {
          setTimeout(() => {
            window.location = ruta + "principal/address";
          }, 1500);
        }
        let type = res.icono === "success" ? 1 : 2;
        alerta(res.msg.toUpperCase(), type);
      }
    };
  };

  // REGISTER
  btnRegister.onclick = function (e) {
    e.preventDefault();

    if (
      dniRegister.value.trim() === "" ||
      nombreRegister.value.trim() === "" ||
      apellidoRegister.value.trim() === "" ||
      direccionRegister.value.trim() === "" ||
      emailRegister.value.trim() === "" ||
      passwordRegister.value.trim() === ""
    ) {
      alerta("TODOS LOS CAMPOS SON REQUERIDOS", 2);
      return;
    }

    if (!/^\d{8}$/.test(dniRegister.value.trim())) {
      alerta("EL DNI DEBE TENER EXACTAMENTE 8 DÍGITOS NUMÉRICOS", 2);
      return;
    }

    let data = new FormData();
    data.append("dni", dniRegister.value.trim());
    data.append("nombre", nombreRegister.value.trim());
    data.append("apellido", apellidoRegister.value.trim());
    data.append("direccion", direccionRegister.value.trim());
    data.append("email", emailRegister.value.trim());
    data.append("clave", passwordRegister.value.trim());

    const url = base_url + "registro/save";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        const res = JSON.parse(this.responseText);
        if (res.icono === "success") {
          setTimeout(() => {
            window.location = base_url + "principal/address";
          }, 1500);
        }
        let type = res.icono === "success" ? 1 : 2;
        alerta(res.msg.toUpperCase(), type);
      }
    };
  };
});
