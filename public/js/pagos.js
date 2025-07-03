const contenedorCarritoProductos = document.querySelector("#carrito-productos");
const contenedorTotal = document.querySelector("#total");

let productos,
  productosjson = [];

document.addEventListener("DOMContentLoaded", function () {
  if (localStorage.getItem("productos-en-carrito") != null) {
    productos = JSON.parse(localStorage.getItem("productos-en-carrito"));
  } else {
    productos = [];
  }
  mostrarProductos();

  numerito.textContent = productos.length;
  numerito1.textContent = productos.length;
});

function mostrarPaypal(totalUSD) {
  paypal
    .Buttons({
      createOrder: (data, actions) => {
        return actions.order.create({
          application_context: {
            shipping_preference: "NO_SHIPPING",
          },
          purchase_units: [
            {
              amount: {
                currency_code: "USD",
                value: totalUSD,
                breakdown: {
                  item_total: {
                    currency_code: "USD",
                    value: totalUSD,
                  },
                },
              },
              items: productosjson,
            },
          ],
        });
      },
      onApprove(data, actions) {
        return actions.order.capture().then(function (orderData) {
          fetch(ruta + "registro/registrarPedido", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              pedidos: orderData,
              productos: productos,
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              alerta(data.msg, 1);
              if (data.icono == "success") {
                productos.length = 0;
                localStorage.setItem("productos-en-carrito", JSON.stringify(productos));
                setTimeout(function () {
                  window.location = ruta + 'principal/complete';
                }, 1500);
              }
            });
        });
      },
    })
    .render("#paypal-button-container");
}

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
      let html = "";
      productosjson = []; // Limpiar productos para PayPal

      data.productos.forEach((producto) => {
        html += `<tr class="table-primary">
          <td class="shoping__cart__item">
            <img src="${ruta + "public/img/productos/" + producto.imagen}" alt="${producto.nombre}" width="50">
            <h5>${producto.nombre}</h5>
          </td>
          <td>${producto.cantidad}</td>
          <td><input class="form-control text-center" type="text" value="${producto.cantidad}" disabled></td>
          <td>S/ ${producto.subTotal}</td>
        </tr>`;

        productosjson.push({
          name: producto.nombre,
          unit_amount: {
            currency_code: "USD",
            value: producto.precio,
          },
          quantity: producto.cantidad,
        });
      });

      contenedorCarritoProductos.innerHTML = html;
      contenedorTotal.textContent = "S/ " + data.total;
      document.getElementById("paypal-button-container").innerHTML = "";
      mostrarPaypal(data.totalPaypal); // en dólares
    });
}