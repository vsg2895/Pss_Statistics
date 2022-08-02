<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pss Statistics</title>
</head>
<body>
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                @foreach($TtuIds as $key => $el)
                    <h5>{{ $el }}</h5>
                @endforeach
            </div>
        </div>
    </div>


</div> <!-- container -->

</body>
</html>

