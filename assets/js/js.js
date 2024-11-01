jQuery(document).ready(function () {
    jQuery('#btnNuke').click(function () {
        jQuery('#nuke').modal('show');
    });

    jQuery('.switch-data').each(function () {
        var btn = jQuery(this).closest('.control-group').find('.btn');
        var btn_active = jQuery(this).closest('.control-group').find('.active');
        var data_value = btn_active.attr('data-value');
        jQuery(this).val(data_value);
        btn.removeClass('btn-success');
        btn_active.addClass('btn-success');
        jQuery(btn.attr('data-target')).collapse('hide');
        btn.each(function () {
            if (!jQuery(this).hasClass('btn-success')) {
                jQuery(jQuery(this).attr('data-target')).collapse('hide');
            }
        });
        jQuery(btn_active.attr('data-target')).collapse('show');
    });

    jQuery('#param_tag_affiliate_network').change(function () {
        jQuery('.param_tag_affiliate_network_custom').collapse('hide');
        if (jQuery(this).val() == 'Custom') {
            jQuery('.param_tag_affiliate_network_custom').collapse('show');
        }
    }).trigger('change');

    jQuery('.switch-data').each(function () {
        var btn = jQuery(this).closest('.control-group').find('.btn');
        var btn_active = jQuery(this).closest('.control-group').find('.active');
        var data_value = btn_active.attr('data-value');
        jQuery(this).val(data_value);
        btn.addClass('btn-default');
        btn.removeClass('btn-success');

        btn_active.removeClass('btn-default');
        btn_active.addClass('btn-success');
        jQuery(btn.attr('data-target')).collapse('hide');
        btn.each(function () {
            if (!jQuery(this).hasClass('btn-success')) {
                jQuery(jQuery(this).attr('data-target')).collapse('hide');
            }
        });
        jQuery(btn_active.attr('data-target')).collapse('show');
    });

    jQuery(".switch button").click(function () {
        var current_button = this;
        var closest_button = jQuery(this).closest('.control-group').find('.btn');
        closest_button.removeClass('btn-success');
        closest_button.removeClass('active');
        closest_button.addClass('btn-default');

        jQuery(this).removeClass('btn-default');
        jQuery(this).addClass('btn-success');
        jQuery(this).closest('.control-group').children('.switch-data').val(jQuery(this).attr('data-value'));
        closest_button.each(function () {
            if (jQuery(this).hasClass('btn-success')) {
                jQuery(jQuery(this).attr('data-target')).collapse('show');
            } else {
                jQuery(jQuery(this).attr('data-target')).collapse('hide');
            }
        });
    });

    jQuery(document.body).on('click', '.confirm', function () {
        var msg = jQuery(this).attr('data-message');
        if (typeof msg !== typeof undefined && msg !== false) {
            return confirm(msg);
        }
        return confirm('Are you sure you want to delete this?');
    });

    jQuery('.cbAll').on('click', function () {
        var selected = this.checked;
        jQuery('input[name^=cbAction]').each(function () {
            this.checked = selected;
        });
    });
    jQuery('.btnAction').on('click', function () {
        var action = jQuery('.slcAction').val();
        var data = new Array();
        if (action == 'Bulk Actions') {
            alert('Please choose your action');
        } else if (jQuery('input[name^=cbAction]:checked').length == 0) {
            alert('Please select at least one record');
        }
        else {
            if (confirm('Are you sure you want to delete this?')) {
                jQuery("input[name^=cbAction]:checked").each(function () {
                    data.push(this.value);
                })

                var request = jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {action: jQuery(this).data('action'), ids: data.join(','), ci_csrf_token: ''},
                    dataType: "json"

                });
                request.done(function (msg) {
                    location.reload();
                });
                request.fail(function (jqXHR, textStatus) {
                    alert("Request failed: " + textStatus);
                });
            }
        }
    });

    jQuery('.dropdown-toggle').dropdown();

    jQuery('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        useCurrent: true,
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'bottom'
        },
    });

    jQuery('.datepickerfrom').datetimepicker({
        format: 'YYYY-MM-DD',
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'bottom'
        },
    });
    jQuery('.datepickerto').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false, //Important! See issue #1075
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'bottom'
        },
    });
    jQuery(".datepickerfrom").on("dp.change", function (e) {
        jQuery('.datepickerto').data("DateTimePicker").minDate(e.date);
    });
    jQuery(".datepickerto").on("dp.change", function (e) {
        jQuery('.datepickerfrom').data("DateTimePicker").maxDate(e.date);
    });



    jQuery('[data-toggle="popover"]').popover({trigger: 'hover', 'placement': 'top'});
    jQuery('[data-toggle="tooltip"]').tooltip({'placement': 'top'});
    jQuery('[rel="tooltip"]').tooltip({'placement': 'top'});

    Highcharts.theme = {
        colors: ['#2980b9', '#c0392b', '#27ae60', '#f39c12', '#8e44ad',
            '#2c3e50', '#16a085', '#f1c40f', '#3498db', '#d35400',
            '#9b59b6', '#34495e', '#95a5a6', '#e74c3c', '#e67e22',
            '#7f8c8d', '#2ecc71', '#bdc3c7', '#1abc9c', '#ecf0f1']
    };
    Highcharts.setOptions(Highcharts.theme);
    Highcharts.setOptions({
        global: {
            useUTC: true
        },
        lang: {
            thousandsSep: ','
        }
    });

});

function download_excel(selector, url) {
    jQuery(selector).click(function () {
        var sSearch = jQuery('.dataTables_filter :input').val();
        var download_url = url + "&download=true&search=" + sSearch;
        window.location.href = download_url;
        return false;
    });
}

function report_chart(selector, series1, series2, height) {
    var minDate = series1[0][0];
    var maxDate = series1[series1.length - 1][0];
    height = typeof height !== 'undefined' ? height : 230;

    jQuery(selector).hide();
    
    var maxValueBar = 0;
    
    jQuery.each(series2, function(index, value){
        if(value[1]>maxValueBar) {
            maxValueBar=value[1];
        }
    });
    maxValueBar += (maxValueBar*10/100);
    
    jQuery(selector).after('<canvas id="myChart" width="100%" height="25"></canvas>');
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            datasets: [{
                yAxisID: 'A',
                label: 'Conversions',
                data: [{
                    x: 1526965990,
                    y: 0,
                }],
                borderWidth: 1,
                backgroundColor: Color("#c0392b").rgbString(),
            }, {
                yAxisID: 'B',
                label: 'Visits',
                data: [{
                    x: 1526965990,
                    y: 0
                }],
                borderColor: '#368fcf',
                borderWidth: '1px',
                backgroundColor: Color("#3998dc").alpha(0.2).rgbString(),
                lineTension: 0,
                type: 'line'
            }]  
        },
        options: {
             legend: {
                display: false
             },
            responsive: true,
            title:{
                display:false,
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ': ';
                        }
                        label += tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        return label;
                    },
                    title: function(tooltipItem, data) {
                        var label = tooltipItem[0].xLabel;
                         var d = new Date(label),
                            month = '' + (d.getMonth() + 1),
                            day = '' + d.getDate(),
                            year = d.getFullYear();
                        if (month.length < 2) month = '0' + month;
                        if (day.length < 2) day = '0' + day;
                        return [day, month, year].join('-');
                    }
                }
            },
            scales: {
                xAxes: [{
                    type: "time",
                    display: true,
                    scaleLabel: {
                        display: false,
                    },
                    offset: true,
                    barPercentage: 0.4
                }],
                yAxes: [{
                    id: 'A',
                    type: 'linear',
                    display: false,
                    ticks: {
                        min:0, max: maxValueBar,
                    },
                    scaleLabel: function(label){return label.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");}
                },{
                    id: 'B',
                    type: 'linear',
                    display: false,
                    scaleLabel: function(label){return label.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");}
                }]
            }
        }
    });
    myChart.data.datasets.forEach((dataset) => {
        dataset.data.pop();
    });
    myChart.update();
    jQuery.each(series1, function(index, value){
        myChart.data.datasets[1].data.push({x: value[0], y: value[1]});
    });
    jQuery.each(series2, function(index, value){
        myChart.data.datasets[0].data.push({x: value[0], y: value[1]});
    });
    myChart.update();
}

function number_format(number, decimals, dec_point, thousands_sep) {
    //  discuss at: http://phpjs.org/functions/number_format/
    // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: davook
    // improved by: Brett Zamir (http://brett-zamir.me)
    // improved by: Brett Zamir (http://brett-zamir.me)
    // improved by: Theriault
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Michael White (http://getsprink.com)
    // bugfixed by: Benjamin Lupton
    // bugfixed by: Allan Jensen (http://www.winternet.no)
    // bugfixed by: Howard Yeend
    // bugfixed by: Diogo Resende
    // bugfixed by: Rival
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    //  revised by: Luke Smith (http://lucassmith.name)
    //    input by: Kheang Hok Chin (http://www.distantia.ca/)
    //    input by: Jay Klehr
    //    input by: Amir Habibi (http://www.residence-mixte.com/)
    //    input by: Amirouche
    //   example 1: number_format(1234.56);
    //   returns 1: '1,235'
    //   example 2: number_format(1234.56, 2, ',', ' ');
    //   returns 2: '1 234,56'
    //   example 3: number_format(1234.5678, 2, '.', '');
    //   returns 3: '1234.57'
    //   example 4: number_format(67, 2, ',', '.');
    //   returns 4: '67,00'
    //   example 5: number_format(1000);
    //   returns 5: '1,000'
    //   example 6: number_format(67.311, 2);
    //   returns 6: '67.31'
    //   example 7: number_format(1000.55, 1);
    //   returns 7: '1,000.6'
    //   example 8: number_format(67000, 5, ',', '.');
    //   returns 8: '67.000,00000'
    //   example 9: number_format(0.9, 0);
    //   returns 9: '1'
    //  example 10: number_format('1.20', 2);
    //  returns 10: '1.20'
    //  example 11: number_format('1.20', 4);
    //  returns 11: '1.2000'
    //  example 12: number_format('1.2000', 3);
    //  returns 12: '1.200'
    //  example 13: number_format('1 000,50', 2, '.', ' ');
    //  returns 13: '100 050.00'
    //  example 14: number_format(1e-8, 8, '.', '');
    //  returns 14: '0.00000001'
    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
                .join('0');
    }
    return s.join(dec);
}


function get_meta(ajax_action_name, url, obj) {
    var text = obj.html();
    var meta_type = obj.data('meta');
    obj.html('<i class="fa fa-spinner fa-pulse"></i>');

    jQuery.ajax({
        url: ajaxurl,
        dataType: 'json',
        type: "POST",
        data: {"action": ajax_action_name, "url": url, "meta_type": meta_type},
//        async: false,
//        cache: false,
//        timeout: 30000,
        beforeSend: function (xhr) {
            obj.html('<i class="fa fa-spinner fa-pulse"></i>');
        }
    }).done(function (json) {
        obj.parent().parent().children('input').val(json.data);
        obj.html(text);
    }).fail(function () {
        obj.parent().parent().children('input').val('unknown');
        obj.html(text);
    });
}

jQuery(document).ajaxStart(function () {
    jQuery(document.body).css({'cursor': 'wait'})
});
jQuery(document).ajaxComplete(function () {
    jQuery(document.body).css({'cursor': 'default'})
});