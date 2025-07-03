<?php include_once 'Views/template/header-admin.php'; ?>

<div class="row">
  <div class="col-sm-12">
    <div class="page-title-box">
      <div class="row align-items-center">
        <div class="col-md-12">
          <h4 class="page-title m-0">Auditoría del Sistema</h4>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover display nowrap align-middle" id="tblAuditoria" style="width: 100%;">
        <thead>
          <tr>
            <th>#</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>Fecha</th>
            <th>Hora</th>
          </tr>
        </thead>
        <tbody>
          <!-- JS llenará esto -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL; ?>public/admin/js/page/auditoria.js"></script>

</body>
</html>