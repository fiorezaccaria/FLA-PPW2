<!DOCTYPE html>
@extends('welcome')

<html lang="en">

<head>
</head>

<body>

    <!--Uploader-->

    <div class="uploader center" >

        <div class="panel panel-primary uplodaer">
            <div class="panel-heading">
                <h2> UPLOAD FILE </h2>
            </div>



                <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
                  @csrf

                            <input type="file" name="file" class="custom-file-input">
                            <button type="submit" class="btn btn-success uploaderBtn">UPLOAD</button>


                      </form>
                    <!--End Uploader-->
</div>
</div>



</body>

</html>
