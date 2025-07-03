const errorBusqueda = document.querySelector("#errorBusqueda");
const inputBuscarNombre = document.querySelector("#buscarProductoNombre");
const tblVenta = document.querySelector("#tblNuevaVenta tbody");

const errorCliente = document.querySelector("#errorCliente");
const direccionCliente = document.querySelector("#direccionCliente");
const idCliente = document.querySelector("#idCliente");
const correoCliente = document.querySelector("#correoCliente");
const buscarCliente = document.querySelector("#buscarCliente");

const pagar_con = document.querySelector("#pagar_con");
const totalPagar = document.querySelector("#totalPagar");
const totalPagarHidden = document.querySelector("#totalPagarHidden");
const cambio = document.querySelector("#cambio");
const btnAccion = document.querySelector("#btnAccion");
const vacio = document.querySelector("#vacio");
const btnVaciar = document.querySelector("#btnVaciar");
const tblNuevaVenta = document.querySelector("#tblNuevaVenta");
const errorPago = document.querySelector("#errorPago"); // Nuevo elemento para mensajes de error en pago

document.addEventListener("DOMContentLoaded", function () {
  // Inicialmente deshabilitar el botón Completar
  btnAccion.setAttribute("disabled", "disabled");
  
  $("#buscarProductoNombre").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: base_url + "ventas/buscarProducto",
        dataType: "json",
        data: {
          term: request.term,
        },
        success: function (data) {
          response(data);
          if (data.length > 0) {
            errorBusqueda.textContent = "";
          } else {
            errorBusqueda.textContent = "NO HAY PRODUCTO CON ESE NOMBRE";
          }
        },
      });
    },
    minLength: 2,
    select: function (event, ui) {
      agregarProducto(ui.item.id);
      inputBuscarNombre.value = "";
      inputBuscarNombre.focus();
      return false;
    },
  });
  
  mostrarCarrito();

  // Autocomplete clientes
  $("#buscarCliente").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: base_url + "ventas/buscarCliente",
        dataType: "json",
        data: {
          term: request.term,
        },
        success: function (data) {
          response(data);
          if (data.length > 0) {
            errorCliente.textContent = "";
          } else {
            errorCliente.textContent = "NO HAY CLIENTE CON ESE NOMBRE";
          }
        },
      });
    },
    minLength: 2,
    select: function (event, ui) {
      direccionCliente.value = ui.item.direccion;
      correoCliente.value = ui.item.correo; 
      idCliente.value = ui.item.id;
      errorCliente.textContent = "";
      validarForma(); // Validar al seleccionar cliente
    },
  });

  // Calcular cambio y validar pago
  pagar_con.addEventListener("keyup", function (e) {
    if (totalPagar.value != "") {
      let totalCambio =
        parseFloat(e.target.value) - parseFloat(totalPagarHidden.value);
      cambio.value = totalCambio.toFixed(2);
    }
    validarPago(); // Validar en cada tecleo
  });

  // Validar pago al perder foco
  pagar_con.addEventListener("blur", validarPago);

  // Evento para completar venta
  btnAccion.addEventListener("click", function () {
    // Validación de cliente
    if (!buscarCliente.value.trim() || !idCliente.value) {
      errorCliente.textContent = "DEBE SELECCIONAR UN CLIENTE";
      buscarCliente.focus();
      return;
    }
    
    // Validación de pago
    if (!validarPago()) {
      pagar_con.focus();
      return;
    }
    
    const url = base_url + "ventas/registrar";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(
      JSON.stringify({
        idCliente: idCliente.value,
        pago: pagar_con.value,
      })
    );
    
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        let type = res.type == "success" ? 1 : 2;
        alertas(res.msg.toUpperCase(), type);
        if (res.type == "success") {
          setTimeout(() => {
            const ruta = base_url + "ventas/ticket/" + res.idVenta;
            PopupCenter(ruta, "Ticket", "600", "500");
            window.location.reload();
          }, 1500);
        }
      }
    };
  });

  // Vaciar carrito
  btnVaciar.addEventListener("click", function () {
    Swal.fire({
      title: "Advertencia?",
      text: "Eliminar productos del carrito!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, eliminar!",
    }).then((result) => {
      if (result.isConfirmed) {
        const url = base_url + "ventas/vaciarCarrito";
        const http = new XMLHttpRequest();
        http.open("GET", url, true);
        http.send();
        http.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let type = res.type == "success" ? 1 : 2;
            alertas(res.msg.toUpperCase(), type);
            mostrarCarrito();
          }
        };
      }
    });
  });
});

// Función para validar el campo de pago
function validarPago() {
  const total = parseFloat(totalPagarHidden.value);
  const pago = parseFloat(pagar_con.value);
  
  // Resetear mensaje de error
  errorPago.textContent = "";
  
  // Validar si está vacío
  if (!pagar_con.value.trim()) {
    errorPago.textContent = "DEBE INGRESAR EL MONTO DE PAGO";
    btnAccion.setAttribute("disabled", "disabled");
    return false;
  }
  
  // Validar si es número
  if (isNaN(pago)) {
    errorPago.textContent = "DEBE INGRESAR UN VALOR NUMÉRICO";
    btnAccion.setAttribute("disabled", "disabled");
    return false;
  }
  
  // Validar si es menor al total
  if (pago < total) {
    errorPago.textContent = "EL PAGO DEBE SER MAYOR O IGUAL AL TOTAL";
    btnAccion.setAttribute("disabled", "disabled");
    return false;
  }
  
  // Si pasa todas las validaciones
  btnAccion.removeAttribute("disabled");
  return true;
}

// Función para validar todo el formulario
function validarForma() {
  const total = parseFloat(totalPagarHidden.value);
  const pago = parseFloat(pagar_con.value);
  
  // Si no hay productos
  if (total <= 0) {
    btnAccion.setAttribute("disabled", "disabled");
    return;
  }
  
  // Si el pago es válido y hay cliente
  if (pago >= total && !isNaN(pago) && idCliente.value) {
    btnAccion.removeAttribute("disabled");
  } else {
    btnAccion.setAttribute("disabled", "disabled");
  }
}

// Función para agregar producto
function agregarProducto(idProducto) {
  const url = base_url + "ventas/agregarProducto/" + idProducto;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      let type = res.type == "success" ? 1 : 2;
      alertas(res.msg.toUpperCase(), type);
      if (res.type == "success") {
        mostrarCarrito();
      }
    }
  };
}

// Función para mostrar carrito
function mostrarCarrito() {
  const url = base_url + "ventas/listarCarrito";
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      if (res.productos.length > 0) {
        let html = "";
        tblNuevaVenta.classList.remove("d-none");
        let mensaje = res.productos.length > 1 ? "PRODUCTOS" : "PRODUCTO";
        vacio.textContent =
          res.productos.length + " " + mensaje + " EN EL CARRITO";
        btnVaciar.removeAttribute("disabled");
        
        for (let i = 0; i < res.productos.length; i++) {
          let subTotal =
            parseFloat(res.productos[i].attributes.price) *
            parseInt(res.productos[i].quantity);
          html += `<tr>
              <td>${res.productos[i].attributes.nombre}</td>
              <td>${res.productos[i].attributes.price}</td>
              <td>${res.productos[i].quantity}</td>
              <td>${subTotal.toFixed(2)}</td>
              <td><button class="btn btn-danger btn-sm" onclick="deleteCart(${
                res.productos[i].id
              })"><i class="fas fa-trash"></i></button></td>
          </tr>`;
        }
        tblVenta.innerHTML = html;
        totalPagarHidden.value = res.totalS;
        totalPagar.value = res.totalF;
      } else {
        tblNuevaVenta.classList.add("d-none");
        vacio.textContent = "No hay productos";
        btnVaciar.setAttribute("disabled", "disabled");
        totalPagarHidden.value = 0;
        totalPagar.value = 0;
        pagar_con.value = "";
        cambio.value = "";
      }
      validarForma(); // Validar después de actualizar
    }
  };
}

// Función para eliminar producto del carrito
function deleteCart(id) {
  const url = base_url + "ventas/deleteCarrito/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      let type = res.type == "success" ? 1 : 2;
      alertas(res.msg.toUpperCase(), type);
      if (res.type == "success") {
        mostrarCarrito();
      }
    }
  };
}

// Función para abrir popup
function PopupCenter(url, title, w, h) {
  // Fixes dual-screen position
  var dualScreenLeft =
    window.screenLeft != undefined ? window.screenLeft : window.screenX;
  var dualScreenTop =
    window.screenTop != undefined ? window.screenTop : window.screenY;

  var width = window.innerWidth
    ? window.screenWidth
    : document.documentElement.clientWidth
    ? document.documentElement.clientWidth
    : screen.width;
  var height = window.innerHeight
    ? window.screenHeight
    : document.documentElement.clientHeight
    ? document.documentElement.clientHeight
    : screen.height;

  var left = width / 2 - w / 2 + dualScreenLeft;
  var top = height / 2 - h / 2 + dualScreenTop;
  var newWindow = window.open(
    url,
    title,
    "scrollbars=yes, width=" +
      w +
      ", height=" +
      h +
      ", top=" +
      top +
      ", left=" +
      left
  );

  // Puts focus on the newWindow
  if (window.focus) {
    newWindow.focus();
  }
}