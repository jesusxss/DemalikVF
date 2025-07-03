<?php include "Views/template/header.php"; ?>

<section class="shoping-cart spad">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-5">Pagos</h5>
                <div class="card">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cant</th>
                                                <th>Precio</th>
                                                <th>SubTotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider" id="carrito-productos">
                                            <!-- Aquí los valores vendrán en soles con S/ desde pagos.js -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                <td><h4 id="total">S/ 0.00</h4></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <!-- PayPal muestra el valor convertido automáticamente a USD -->
                                <div id="paypal-button-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "Views/template/footer.php"; ?>

<script src="<?php echo BASE_URL; ?>public/admin/js/jquery.min.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID; ?>&currency=USD"></script>
<script src="<?php echo BASE_URL; ?>public/js/pagos.js"></script>
</body>
</html>