<script type="text/javascript" src="<?php echo base_url('assets/ext/jquery/jquery.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var passedTestsNumber = $('span').filter(function() {
                                    return $(this).text() === 'Passed';
                                }).length;
        var totalTestsNumber = $('table').length;

        $('#test-results').text(passedTestsNumber + ' / ' + totalTestsNumber + ' Passed');

        if (passedTestsNumber == totalTestsNumber) {
            $('#test-header').css('background-color', '#3DD481');
        } else {
            $('#test-header').css('background-color', '#D43D3D');
        }
    });
</script>

<style>
    #test-header {
        font-size: 30px;
        font-family: arial, sans-serif;
        height: 70px;
        padding: 10px;
        margin-bottom: 15px;
    }

    #test-heading {
        margin-top: 15px;
        display: inline-block;
        margin-right: 21px;
        color: #DBFFA6;
    }

    #test-results {
        color: #FFF;
    }
</style>

<div id="test-header">
    <strong id="test-heading">Easy!Appointments Unit Testing</strong>
    <strong id="test-results"></strong>
</div>
