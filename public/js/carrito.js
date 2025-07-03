let botonesEliminar = document.querySelectorAll(".carrito-eliminar");
const botonVaciar = document.querySelector("#botonVaciar");
const contenedorTotal = document.querySelector("#total");
const tblCarrito = document.querySelector("#tblCarrito");
const botonwhatsapp = document.querySelector("#carrito-whatsapp");
const whatsapp_negocio = document.querySelector("#whatsapp-negocio");
let productosWhatsapp, productosTotal;

document.addEventListener("DOMContentLoaded", function () {
  if (localStorage.getItem("productos-en-carrito") != null) {
    productos = JSON.parse(localStorage.getItem("productos-en-carrito"));
  } else {
    productos = [];
  }
  mostrarProductos();

  botonVaciar.addEventListener("click", vaciarCarrito);

  const botonCheckout = document.querySelector(".primary-btn.mb-2");
  if (botonCheckout) {
    botonCheckout.addEventListener("click", function (e) {
      fetch(ruta + "principal/syncCarrito", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(productos),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status === "ok") {
            window.location.href = ruta + "principal/address";
          } else {
            alerta("No se pudo sincronizar el carrito", 2);
          }
        });

      e.preventDefault();
    });
  }
});

function mostrarProductos() {
  fetch(ruta + "principal/listaProductos", {
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(productos),
    method: "POST",
  })
    .then((response) => response.json())
    .then((data) => {
      productosWhatsapp = "*Productos del pedido:*%0A";
      let html = '';
      if (data.productos.length > 0) {
        data.productos.forEach((producto) => {
          productosWhatsapp += `- ${producto.cantidad} x ${producto.nombre} = S/ ${producto.precio}%0A`;
          html += `<tr>
            <td class="shoping__cart__item">
                <img src="${ruta + "public/img/productos/" + producto.imagen}" alt="${producto.nombre}" width="50">
                <h5>${producto.nombre}</h5>
            </td>
            <td class="shoping__cart__price">
                S/ ${producto.precio}
            </td>
            <td class="shoping__cart__quantity">
                <div class="quantity">
                    <div class="pro-qty">
                        <input type="text" value="${producto.cantidad}">
                    </div>
                </div>
            </td>
            <td class="shoping__cart__total">
                S/ ${producto.subTotal}
            </td>
            <td class="shoping__cart__item__close carrito-eliminar">
                <span class="icon_close"></span>
            </td>
        </tr>`;
        });
        productosTotal = `*Total:* S/ ${data.total}`;
      } else {
        html = `<tr>
          <td colspan="5">
            CARRITO VACIO
        </td>`;
      }
      tblCarrito.innerHTML = html;
      contenedorTotal.textContent = "S/ " + data.total;
      actualizarBotonesEliminar();
    });

  botonwhatsapp.addEventListener("click", function (e) {
    e.preventDefault();
    Swal.fire({
      title: "¿Estás seguro realizar pedido por whatsapp?",
      icon: "question",
      showCancelButton: true,
      focusConfirm: false,
      confirmButtonText: "Sí",
      cancelButtonText: "No",
    }).then((result) => {
      if (result.isConfirmed) {
        window.open(
          `https://api.whatsapp.com/send?phone=51999065445&text=${productosWhatsapp}%0A${productosTotal}%0A%0AGracias por tu pedido `,
          "_blank"
        );
      }
    });
  });
}

function actualizarBotonesEliminar() {
  botonesEliminar = document.querySelectorAll(".carrito-eliminar");
  botonesEliminar.forEach((boton) => {
    boton.addEventListener("click", eliminarDelCarrito);
  });
}

function eliminarDelCarrito(e) {
  const idBoton = e.currentTarget.id;
  const index = productos.findIndex((producto) => producto.id === idBoton);
  productos.splice(index, 1);
  mostrarProductos();
  localStorage.setItem("productos-en-carrito", JSON.stringify(productos));
  alerta("Producto eliminado", 1);
}

function vaciarCarrito() {
  Swal.fire({
    title: "¿Estás seguro?",
    icon: "question",
    html: `Se van a borrar ${productos.length} productos.`,
    showCancelButton: true,
    focusConfirm: false,
    confirmButtonText: "Sí",
    cancelButtonText: "No",
  }).then((result) => {
    if (result.isConfirmed) {
      productos.length = 0;
      localStorage.setItem("productos-en-carrito", JSON.stringify(productos));
      mostrarProductos();
      alerta("Productos eliminados", 1);
      setTimeout(() => {
        window.location.reload();
      }, 1500);
    }
  });
}

function alerta(mensaje, type) {
  let color = type == 1 ? "#46cd93" : "#f24734";
  Toastify({
    text: mensaje,
    duration: 3000,
    close: true,
    gravity: "top",
    position: "right",
    stopOnFocus: true,
    style: {
      background: color,
      borderRadius: "2rem",
      textTransform: "uppercase",
      fontSize: ".75rem",
    },
    offset: {
      x: "1.5rem",
      y: "1.5rem",
    },
  }).showToast();
}
