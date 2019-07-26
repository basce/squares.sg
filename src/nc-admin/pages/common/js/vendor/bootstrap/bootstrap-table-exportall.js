/**
 * @author zhixin wen <wenzhixin2010@gmail.com>
 * extensions: https://github.com/kayalshri/tableExport.jquery.plugin
 */

(function ($) {
    'use strict';

    var TYPE_NAME = {
        json: 'JSON',
        xml: 'XML',
        png: 'PNG',
        csv: 'CSV',
        txt: 'TXT',
        sql: 'SQL',
        doc: 'MS-Word',
        excel: 'Ms-Excel',
        powerpoint: 'Ms-Powerpoint',
        pdf: 'PDF'
    };

    $.extend($.fn.bootstrapTable.defaults, {
        showExportAll: false,
        // 'json', 'xml', 'png', 'csv', 'txt', 'sql', 'doc', 'excel', 'powerpoint', 'pdf'
        allExportTypes: ['csv']
    });

    var BootstrapTable = $.fn.bootstrapTable.Constructor,
        _initToolbar = BootstrapTable.prototype.initToolbar;

    BootstrapTable.prototype.initToolbar = function () {
        this.showToolbar = true;

        _initToolbar.apply(this, Array.prototype.slice.apply(arguments));

        if (this.options.showExportAll) {
            var that = this,
                $btnGroup = this.$toolbar.find('>.btn-group'),
                $export = $btnGroup.find('div.exportAll');

            if (!$export.length) {
                $export = $([
                    '<div class="exportAll btn-group">',
                        '<button class="btn btn-default dropdown-toggle" ' +
                            'data-toggle="dropdown" type="button" title="full report">',
                            '<i class="glyphicon glyphicon-new-window icon-share"></i> ',
                            '<span class="caret"></span>',
                        '</button>',
                        '<ul class="dropdown-menu" role="menu">',
                        '</ul>',
                    '</div>'].join('')).appendTo($btnGroup);

                var $menu = $export.find('.dropdown-menu'),
                    allExportTypes = this.options.allExportTypes;

                if (typeof this.options.allExportTypes === 'string') {
                    var types = this.options.allExportTypes.slice(1, -1).replace(/ /g, '').split(',');

                    allExportTypes = [];
                    $.each(types, function (i, value) {
                        allExportTypes.push(value.slice(1, -1));
                    });
                }
                $.each(allExportTypes, function (i, type) {
                    if (TYPE_NAME.hasOwnProperty(type)) {
                        if(type == "csv"){
                            $menu.append(['<li data-type="' + type + '">',
                                '<a href="./?method=csvreport&all=1" target="_blank">',
                                    TYPE_NAME[type],
                                '</a>',
                            '</li>'].join(''));
                        }else{
                            $menu.append(['<li data-type="' + type + '">',
                                '<a href="javascript:void(0)">',
                                    TYPE_NAME[type],
                                '</a>',
                            '</li>'].join(''));
                        }
                    }
                });
				
                $menu.find('li').click(function () {
					var temptype = $(this).data('type');
                    if(temptype == "csv"){
                        return;
                    }
					$.get(that.options.url,{all:true, type:temptype, method:that.options.ncmethod},function(result){
						if(result.rows && result.rows.length != 0 ){
							//build invisible table
							var tempcontainer = $("<div>");
							var temptable = $("<table>");
							var tempheader = $("<thead>");
							var tempbody = $("<tbody>");
							var temprow = $("<tr>");
							
							//create header	
							temprow = $("<tr>");
							for( var i in result.rows[0]){
								temprow.append($("<th>").text(i));
							}
							tempheader.append(temprow);
							
							//create body
							for( var i = 0; i < result.rows.length; i++){
								temprow = $("<tr>");
								for( var j in result.rows[i] ){
									temprow.append($("<td>").text(result.rows[i][j]));
								}
								tempbody.append(temprow);
							}
							temptable.append(tempheader);
							temptable.append(tempbody);
							tempcontainer.append(temptable);
							that.$el.after(tempcontainer);
							temptable.tableExport({
								tableName:that.options.exportfilename,
								type: temptype,
								escape: false
							});
							tempcontainer.remove();
						}else{
							alert("empty data");
						}
					},"json");
                });
            }
        }
    };
})(jQuery);