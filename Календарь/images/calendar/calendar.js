$(function (){ $(document).click(function(e){ if ($(e.target).parents().filter('#menu').length != 1) { $('#cliplayer').fadeOut('slow'); } }); $('#show_mmd').live('click',function(e){ $('#cliplayer').fadeToggle('slow'); return false;}); if ($("div").is("#ajax_preloader")==false) { $('<div id="ajax_preloader" class="ajax_preloader"><span>Загрузка... Дождитесь завершения!</span></div>').appendTo("body"); } });
function navigate(month,year,mod) { var f = "ajax.php?op=calendar&cajax="+mod+"&cal_date="+year+"-"+month; $.ajax({ type: 'get', url: f, beforeSend: function () {curtainClose(); $('#ajax_preloader').fadeIn('slow');}, complete: function () {$('#ajax_preloader').fadeOut('slow');}, success: function (d) {$('#calendar').html(d);} }); }
function curtainClose(){ $('#cliplayer').fadeOut('slow'); }
function curtainOpen() { $('#cliplayer').fadeIn('slow'); }