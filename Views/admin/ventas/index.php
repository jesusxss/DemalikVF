<?php 
include_once 'Views/template/header-admin.php'; ?>

<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <h4 class="page-title m-0">Ventas</h4>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end page-title-box -->
    </div>
</div>
<!-- end page title -->

<div class="card">
    <div class="card-body">
        <h5 class="card-title text-center"><i class="fas fa-cash-register"></i> Nueva Venta</h5>
        <hr>

        <div class="row">
            <div class="col-lg-8">
                <!-- input para buscar nombre -->
                <div class="input-group mb-2" id="containerNombre">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input class="form-control" type="text" id="buscarProductoNombre" placeholder="Buscar Producto" autocomplete="off">
                </div>
                <span class="text-danger fw-bold mb-2" id="errorBusqueda"></span>
                <!-- table productos -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle" id="tblNuevaVenta" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>SubTotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <div class="card text-white bg-info">
                      <div class="card-body text-center">
                        <h4 class="card-title" id="vacio"></h4>
                        <button id="btnVaciar" class="btn btn-danger" type="button" disabled><i class="fas fa-recycle"></i> Vaciar</button>
                      </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">

                <label>Buscar Cliente</label>
                <div class="input-group mb-2">
                    <input type="hidden" id="idCliente">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input class="form-control" type="text" id="buscarCliente" placeholder="Buscar Cliente">
                </div>
                <span class="text-danger fw-bold mb-2" id="errorCliente"></span>

                <label>Dirección</label>
                <div class="input-group mb-2">
                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                    <input class="form-control" type="text" id="direccionCliente" placeholder="Dirección" disabled>
                </div>
                <label>Correo</label>
                <div class="input-group mb-2">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input class="form-control" type="text" id="correoCliente" placeholder="Correo" disabled>
                </div>

                    <label>Pagar con</label>
        <div class="input-group mb-2">
            <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="13.000000pt" height="16.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
                    <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#495057" stroke="none">
                        <path d="M1203 4420 c-637 -51 -1078 -421 -1122 -942 -25 -308 73 -561 301 -773 160 -148 360 -260 698 -389 113 -43 231 -92 263 -108 216 -108 320 -227 335 -386 26 -255 -99 -420 -373 -493 -65 -17 -111 -22 -245 -22 -254 -2 -434 40 -669 155 -108 53 -129 60 -165 55 -67 -9 -85 -39 -161 -266 -89 -268 -86 -282 71 -360 382 -190 935 -247 1375 -143 632 151 980 666 862 1277 -60 316 -268 548 -668 748 -49 24 -182 81 -295 127 -257 103 -369 158 -456 223 -120 91 -164 173 -164 308 0 99 29 170 99 240 103 103 243 149 450 149 199 0 357 -30 505 -96 148 -66 154 -67 202 -49 23 9 48 22 54 30 22 26 150 400 150 438 0 59 -25 86 -125 133 -235 111 -605 169 -922 144z"></path>
                        <path d="M3965 4358 c-33 -30 -1465 -3493 -1465 -3544 0 -60 17 -64 251 -64 192 0 207 1 224 20 9 10 345 808 747 1772 582 1400 727 1758 722 1780 -11 51 -41 58 -258 58 -188 0 -196 -1 -221 -22z"></path>
                        <path d="M4613 1555 c-63 -19 -124 -58 -169 -107 -179 -194 -107 -525 135 -620 81 -32 223 -31 306 2 83 32 174 123 204 204 82 220 -17 449 -226 517 -66 22 -187 24 -250 4z"></path>
                    </g>
                </svg>
            </span>
            <input class="form-control" type="text" id="pagar_con" placeholder="0.00">
        </div>
        <div id="errorPago" class="text-danger small mt-1"></div>
                <label>Cambio</label>
                <div class="input-group mb-2">
                    <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="13.000000pt" height="16.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
<g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#495057" stroke="none">
<path d="M1203 4420 c-637 -51 -1078 -421 -1122 -942 -25 -308 73 -561 301 -773 160 -148 360 -260 698 -389 113 -43 231 -92 263 -108 216 -108 320 -227 335 -386 26 -255 -99 -420 -373 -493 -65 -17 -111 -22 -245 -22 -254 -2 -434 40 -669 155 -108 53 -129 60 -165 55 -67 -9 -85 -39 -161 -266 -89 -268 -86 -282 71 -360 382 -190 935 -247 1375 -143 632 151 980 666 862 1277 -60 316 -268 548 -668 748 -49 24 -182 81 -295 127 -257 103 -369 158 -456 223 -120 91 -164 173 -164 308 0 99 29 170 99 240 103 103 243 149 450 149 199 0 357 -30 505 -96 148 -66 154 -67 202 -49 23 9 48 22 54 30 22 26 150 400 150 438 0 59 -25 86 -125 133 -235 111 -605 169 -922 144z"></path>
<path d="M3965 4358 c-33 -30 -1465 -3493 -1465 -3544 0 -60 17 -64 251 -64 192 0 207 1 224 20 9 10 345 808 747 1772 582 1400 727 1758 722 1780 -11 51 -41 58 -258 58 -188 0 -196 -1 -221 -22z"></path>
<path d="M4613 1555 c-63 -19 -124 -58 -169 -107 -179 -194 -107 -525 135 -620 81 -32 223 -31 306 2 83 32 174 123 204 204 82 220 -17 449 -226 517 -66 22 -187 24 -250 4z"></path>
</g>
</svg></span>
                    <input class="form-control" type="text" id="cambio" placeholder="0.00" readonly>
                </div>
                <label>Total a Pagar</label>
                <div class="input-group mb-2">
                    <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="13.000000pt" height="16.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet">
<g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="#495057" stroke="none">
<path d="M1203 4420 c-637 -51 -1078 -421 -1122 -942 -25 -308 73 -561 301 -773 160 -148 360 -260 698 -389 113 -43 231 -92 263 -108 216 -108 320 -227 335 -386 26 -255 -99 -420 -373 -493 -65 -17 -111 -22 -245 -22 -254 -2 -434 40 -669 155 -108 53 -129 60 -165 55 -67 -9 -85 -39 -161 -266 -89 -268 -86 -282 71 -360 382 -190 935 -247 1375 -143 632 151 980 666 862 1277 -60 316 -268 548 -668 748 -49 24 -182 81 -295 127 -257 103 -369 158 -456 223 -120 91 -164 173 -164 308 0 99 29 170 99 240 103 103 243 149 450 149 199 0 357 -30 505 -96 148 -66 154 -67 202 -49 23 9 48 22 54 30 22 26 150 400 150 438 0 59 -25 86 -125 133 -235 111 -605 169 -922 144z"></path>
<path d="M3965 4358 c-33 -30 -1465 -3493 -1465 -3544 0 -60 17 -64 251 -64 192 0 207 1 224 20 9 10 345 808 747 1772 582 1400 727 1758 722 1780 -11 51 -41 58 -258 58 -188 0 -196 -1 -221 -22z"></path>
<path d="M4613 1555 c-63 -19 -124 -58 -169 -107 -179 -194 -107 -525 135 -620 81 -32 223 -31 306 2 83 32 174 123 204 204 82 220 -17 449 -226 517 -66 22 -187 24 -250 4z"></path>
</g>
</svg></span>
                    <input class="form-control" type="text" id="totalPagar" placeholder="Total Pagar" disabled>
                    <input class="form-control" type="hidden" id="totalPagarHidden">
                </div>

                <button class="btn btn-primary btn-block" type="button" id="btnAccion">Completar</button>

            </div>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'public/admin/js/jquery-ui.min.js'; ?>"></script>
<script src="<?php echo BASE_URL . 'public/admin/js/page/ventas.js'; ?>"></script>

</body>

</html>