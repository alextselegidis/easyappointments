$(function () {
    'use strict';

    var $modal = $('#working-plan-periods-modal');
    var $startdate = $('#working-plan-periods-startdate');
    var $enddate = $('#working-plan-periods-enddate');
    var $save = $('#working-plan-periods-save');
    var deferred = null;
    var enableSubmit = false;
    var enableCancel = false;

    function resetModal() {
        $startdate.val('');
        $enddate.val('');
    }

    function validate() {
        $modal.find('.is-invalid').removeClass('is-invalid');

        var startdate = $startdate.datepicker('getDate');

        if (!startdate) {
            $startdate.addClass('is-invalid');
        }

        var enddate = $enddate.datepicker('getDate');

        if (!enddate) {
            $enddate.addClass('is-invalid');
        }

        return !$modal.find('.is-invalid').length;
    }

    function onModalHidden() {
        resetModal();
    }

    function onSaveClick() {
        if (!deferred) {
            return;
        }

        if (!validate()) {
            return;
        }

        var startdate = $startdate.datepicker('getDate').toString('yyyy-MM-dd');

        var workingPlanPeriod = {
            enddate: $enddate.datepicker('getDate').toString('yyyy-MM-dd')
        };

        deferred.resolve(startdate, workingPlanPeriod);

        $modal.modal('hide');
        resetModal();
    }


    function add() {
        deferred = jQuery.Deferred();

        $startdate.datepicker('setDate', new Date());
        $enddate.datepicker('setDate', new Date());

        $modal.modal('show');

        return deferred.promise();
    }

    function edit(startdate, workingPlanPeriod) {
        deferred = jQuery.Deferred();

        $startdate.datepicker('setDate', moment(startdate, 'YYYY-MM-DD').toDate());
        $enddate.datepicker('setDate', moment(workingPlanPeriod.enddate, 'YYYY-MM-DD').toDate());

        $modal.modal('show');

        return deferred.promise();
    }




    function initializeDatepicker($target) {
        var dateFormat;

        switch (GlobalVariables.dateFormat) {
            case 'DMY':
                dateFormat = 'dd/mm/yy';
                break;

            case 'MDY':
                dateFormat = 'mm/dd/yy';
                break;

            case 'YMD':
                dateFormat = 'yy/mm/dd';
                break;

            default:
                throw new Error('Invalid date format setting provided: ' + GlobalVariables.dateFormat);
        }

        $target.datepicker({
            dateFormat: dateFormat,
            firstDay: GeneralFunctions.getWeekDayId(GlobalVariables.firstWeekday),
            minDate: 0,
            defaultDate: Date.today(),
            dayNames: [
                EALang.sunday, EALang.monday, EALang.tuesday, EALang.wednesday,
                EALang.thursday, EALang.friday, EALang.saturday],
            dayNamesShort: [EALang.sunday.substr(0, 3), EALang.monday.substr(0, 3),
                EALang.tuesday.substr(0, 3), EALang.wednesday.substr(0, 3),
                EALang.thursday.substr(0, 3), EALang.friday.substr(0, 3),
                EALang.saturday.substr(0, 3)],
            dayNamesMin: [EALang.sunday.substr(0, 2), EALang.monday.substr(0, 2),
                EALang.tuesday.substr(0, 2), EALang.wednesday.substr(0, 2),
                EALang.thursday.substr(0, 2), EALang.friday.substr(0, 2),
                EALang.saturday.substr(0, 2)],
            monthNames: [EALang.january, EALang.february, EALang.march, EALang.april,
                EALang.may, EALang.june, EALang.july, EALang.august, EALang.september,
                EALang.october, EALang.november, EALang.december],
            prevText: EALang.previous,
            nextText: EALang.next,
            currentText: EALang.now,
            closeText: EALang.close,
        });
    }

    initializeDatepicker($startdate);
    initializeDatepicker($enddate);

    $modal
        .on('hidden.bs.modal', onModalHidden)

    $save.on('click', onSaveClick);

    window.WorkingPlanPeriodsModal = {
        add: add,
        edit: edit,
    };
});
