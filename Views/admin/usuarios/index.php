<?php include_once 'Views/template/header-admin.php'; ?>

<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <h4 class="page-title m-0">Usuarios</h4>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end page-title-box -->
    </div>
</div>
<!-- end page title -->

<?php if ($_SESSION['tipo'] == 1): ?>
  <button class="btn btn-primary mb-2" type="button" id="nuevo_registro">Nuevo</button>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblUsuarios">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombres</th>
                        <th>ROL</th>
                        <th>Correo</th>
                        <th>Dirección</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="nuevoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="titleModal"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>


            <form id="frmRegistro" autocomplete="off">
  <div class="modal-body row">
    <input type="hidden" id="id" name="id">

    <!-- NOMBRE -->
    <div class="form-group col-lg-6 mb-2">
      <label for="nombre">Nombre</label>
      <input id="nombre" name="nombre"
             class="form-control" type="text" placeholder="Nombres">
    </div>

    <!-- APELLIDO -->
    <div class="form-group col-lg-6 mb-2">
      <label for="apellido">Apellido</label>
      <input id="apellido" name="apellido"
             class="form-control" type="text" placeholder="Apellidos">
    </div>

    <!-- ROL (TIPO) -->
    <div class="form-group col-lg-6 mb-2">
      <label for="rol">ROL</label>
      <select id="rol" name="rol"  class="form-control" required>
        <option value="">Seleccione</option>
        <option value="ADMINISTRADOR">ADMINISTRADOR</option>
        <option value="PERSONAL">PERSONAL</option>
      </select>
    </div>

    <!-- CORREO -->
    <div class="form-group col-lg-6 mb-2">
      <label for="correo">Correo</label>
      <input id="correo" name="correo"
             class="form-control" type="email" placeholder="Correo electrónico" required>
    </div>

    <!-- DIRECCIÓN -->
    <div class="form-group col-lg-6 mb-2">
      <label for="direccion">Dirección</label>
      <input id="direccion" name="direccion"
             class="form-control" type="text" placeholder="Dirección">
    </div>

    <!-- CLAVE -->
    <div class="form-group col-lg-6 mb-2">
      <label for="clave">Contraseña</label>
      <input id="clave" name="clave"
             class="form-control" type="password" placeholder="Contraseña">
    </div>
  </div>

  <div class="modal-footer">
    <button class="btn btn-primary" type="submit" id="btnAccion">Registrar</button>
    <button class="btn btn-danger"  type="button" data-dismiss="modal">Cancelar</button>
  </div>
</form>


        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script>
  const tipo_usuario = <?php echo $_SESSION['tipo']; ?>;
</script>

<script src="<?php echo BASE_URL . 'public/admin/js/page/usuarios.js'; ?>"></script>

</body>

</html>