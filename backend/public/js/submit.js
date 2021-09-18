$("form").submit(function() {
    var self = this;
    $(":submit", self).prop("disabled", true);
    setTimeout(function() {
      $(":submit", self).prop("disabled", false);
    }, 10000);
});