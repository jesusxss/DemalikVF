let tblAuditoria;

document.addEventListener("DOMContentLoaded", function () {
  tblAuditoria = $("#tblAuditoria").DataTable({
    ajax: {
      url: base_url + "auditoria/listar",
      dataSrc: ""
    },
    columns: [
      { data: "id" },
      { data: "usuario" },
      { data: "accion" },
      { data: "fecha" },
      { data: "hora" }
    ],
    responsive: true,
    language,
    dom: 'Bfrtip',
    buttons
  });
});