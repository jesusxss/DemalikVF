let tblUsuarios;

const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");
const btnNuevo = document.querySelector("#btnNuevo");

const nombre     = document.querySelector("#nombre");
const apellido   = document.querySelector("#apellido");   // <— nuevo
const tipo       = document.querySelector("#rol");        // <— nuevo nombre claro
const clave      = document.querySelector("#clave");
const correo     = document.querySelector("#correo");
const direccion  = document.querySelector("#direccion");
const id         = document.querySelector("#id");

const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));

document.addEventListener("DOMContentLoaded", function () {
  //cargar datos con el plugin datatables
  tblUsuarios = $("#tblUsuarios").DataTable({
    ajax: {
      url: base_url + "usuarios/listar",
      dataSrc: "",
    },
    columns: [
  { data: "item" },
  { data: "nombre" },
  { data: "rol" },          //  ← antes decía "apellido"
  { data: "correo" },
  { data: "direccion" },
  { data: "accion" },
],
    language,
    dom: "Bfrtip",
    buttons,
    responsive: true,
    order: [[0, "desc"]],
  });

  //limpiar campos
  nuevo.addEventListener("click", function () {
    id.value = "";
    titleModal.textContent = "NUEVO USUARIO";
    btnAccion.textContent = "Registrar";
    clave.removeAttribute("readonly");
    frm.reset();
    myModal.show();
  });


  //registrar usuarios
  frm.addEventListener("submit", function (e) {
    e.preventDefault();
    if (
  nombre.value.trim()     === "" ||
  apellido.value.trim()   === "" ||
  tipo.value.trim()       === "" ||
  clave.value.trim()      === "" ||
  correo.value.trim()     === "" ||
  direccion.value.trim()  === ""
) {
  alertas("TODOS LOS CAMPOS SON REQUERIDOS", 2);
  return;
} else {

      const url  = base_url + "usuarios/registrar";
const http = new XMLHttpRequest();
http.open("POST", url, true);

const datos = new FormData(this);   // ← puedes dejar el console.log
// console.log([...datos.entries()]);

http.send(datos);                   // ← solo UNA llamada

http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
        console.log("🟡 RESPUESTA DEL SERVIDOR:", this.responseText);

        const res = JSON.parse(this.responseText);
        if (res.icono == "success") {
            tblUsuarios.ajax.reload();    // ya se refrescará la tabla
            frm.reset();                  // limpia el formulario
            id.value = "";
            btnAccion.textContent = "Registrar";
            myModal.hide();
        }
        alertas(res.msg.toUpperCase(), res.icono == "success" ? 1 : 2);
    }
};
    }
  });
});

function eliminarUser(idUsuario) {
  if (tipo_usuario == 3) {
    alertas("No tienes permisos para eliminar usuarios", 2);
    return;
  }

  Swal.fire({
    title: "¿Estás seguro?",
    text: "¡Esta acción no se puede deshacer!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar"
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "usuarios/delete/" + idUsuario;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblUsuarios.ajax.reload();
          }
          let type = res.icono == "success" ? 1 : 2;
          alertas(res.msg.toUpperCase(), type);
        }
      };
    }
  });
}

function editUser(idUsuario) {
  const url = base_url + "usuarios/editar/" + idUsuario;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();

  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      id.value = res.id;
      nombre.value = res.nombre;
      apellido.value = res.apellido;
      clave.value = "00000000000";
      clave.setAttribute("readonly", "readonly");
      correo.value = res.correo;
      direccion.value = res.direccion;

      // 🔽 Esta línea es la nueva: para mostrar correctamente el rol
      tipo.value = res.tipo;

      btnAccion.textContent = "Actualizar";
      myModal.show();
    }
  };
}
