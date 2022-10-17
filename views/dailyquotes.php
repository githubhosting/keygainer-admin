<h2>Premium Quotes</h2>

<hr />

<div class="row">

    <div class="col-lg-5">
        <h4>Quotes of The Day</h4>
        <form id="category-form">
            <div class="form-group">
                <label for="category-name">Enter name</label>
                <input type="text" value="quotes" class="form-control" id="category-name" disabled/>
                
                <div class="invalid-feedback">
                    Please enter a category name
                </div>
            </div>
            <div class="form-group">
                <label for="category-desc">Today Quote</label>
                <input type="text" class="form-control" id="category-desc" />
                <div class="invalid-feedback">
                    Please enter some short Quote for Quote of the day
                </div>
            </div>
            <div class="form-group">
                <label for="category-thumbnail">Thumbnail</label>
                <input type="file" class="form-control" id="category-thumbnail" />
                <div class="invalid-feedback">
                    Please choose a valid image thumbnail
                </div>
            </div>

            <div class="form-group">
                <img id="selected-thumbnail" width="250" height="150" src="#" />
            </div>

            <div class="form-group">
                <div class="progress">
                    <div class="progress-bar" id="upload-progress" style="width:0%">0%</div>
                </div>
            </div>
            <div class="from-group">
                <button id="save-category" type="button" class="btn btn-primary">Save</button>
            </div>
        </form>
        <div id="result">

        </div>
    </div>

    <div class="col-lg-7">
        <h4>Today's Quote</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Quote Text</th>
                    <th>Quote image</th>
                </tr>
            </thead>
            <tbody id="categories">

            </tbody>
        </table>
    </div>

</div>

<script>

    var validImageTypes = ["image/gif", "image/jpeg", "image/png"];

    $("#selected-thumbnail").hide();

    function previewThumbnail(thumbnail){
        if(thumbnail.files && thumbnail.files[0]){
            var reader = new FileReader(); 

            reader.onload = function(e){
                $("#selected-thumbnail").attr('src', e.target.result);
                $("#selected-thumbnail").fadeIn();
            }
            reader.readAsDataURL(thumbnail.files[0]);
        }
    }

    $("#category-thumbnail").change(function(){
        previewThumbnail(this);
    });

    $("#save-category").click(function(){
        $("#category-name").removeClass("is-invalid");
        $("#category-desc").removeClass("is-invalid");
        $("#category-thumbnail").removeClass("is-invalid");

        var catname = $("#category-name").val();
        var desc = $("#category-desc").val(); 
        var thumbnail = $("#category-thumbnail").prop("files")[0];

        if(!catname){
            $("#category-name").addClass("is-invalid");
            return; 
        }

        if(!desc){
            $("#category-desc").addClass("is-invalid");
            return; 
        }

        if(thumbnail == null){
            $("#category-thumbnail").addClass("is-invalid");
            return; 
        }

        if($.inArray(thumbnail["type"], validImageTypes)<0){
            $("#category-thumbnail").addClass("is-invalid");
            return; 
        }

        var database = firebase.database().ref("quoteoftheday/"+catname);

        database.once("value").then(function(snapshot){
            
            if(snapshot.exists()){
                var name = thumbnail["name"];
                var ext = name.substring(name.lastIndexOf("."), name.length);

                var thumbname = new Date().getTime(); 

                var storageRef = firebase.storage().ref(catname + "/" + thumbname + ext);

                var uploadTask = storageRef.put(thumbnail);

                uploadTask.on("state_changed", 

                    function progress(snapshot){
                        var percentage = (snapshot.bytesTransferred / snapshot.totalBytes) * 100; 

                        $("#upload-progress").html(Math.round(percentage) + "%");
                        $("#upload-progress").attr("style", "width:"+percentage + "%");
                    }, 

                    function error(err){

                    }, 

                    function complete(){
                        uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
                            var cat = {
                                "thumbnail": downloadURL, 
                                "desc": desc,
                                "id" : <?php echo(rand(10,1000));
?>
                            };

                            database.set(cat, function(err){
                                if(err){
                                    $("#result").attr("class", "alert alert-danger");
                                    $("#result").html(err.message);
                                }else{
                                    $("#result").attr("class", "alert alert-success");
                                    $("#result").html("Category added");
                                }
                                resetForm();
                            }); 
                        });
                    }
                
                );

            
            }else{
                //1. upload the selected thumbnail to firebase storage
                var name = thumbnail["name"];
                var ext = name.substring(name.lastIndexOf("."), name.length);

                var thumbname = new Date().getTime(); 

                var storageRef = firebase.storage().ref(catname + "/" + thumbname + ext);

                var uploadTask = storageRef.put(thumbnail);

                uploadTask.on("state_changed", 

                    function progress(snapshot){
                        var percentage = (snapshot.bytesTransferred / snapshot.totalBytes) * 100; 

                        $("#upload-progress").html(Math.round(percentage) + "%");
                        $("#upload-progress").attr("style", "width:"+percentage + "%");
                    }, 

                    function error(err){

                    }, 

                    function complete(){
                        uploadTask.snapshot.ref.getDownloadURL().then(function(downloadURL) {
                            var cat = {
                                "thumbnail": downloadURL, 
                                "desc": desc
                            };

                            database.set(cat, function(err){
                                if(err){
                                    $("#result").attr("class", "alert alert-danger");
                                    $("#result").html(err.message);
                                }else{
                                    $("#result").attr("class", "alert alert-success");
                                    $("#result").html("Category added");
                                }
                                resetForm();
                            }); 
                        });
                    }
                
                );

            }

        });

    });

    function resetForm(){
       $("#category-form")[0].reset(); 
       $("#selected-thumbnail").fadeOut();
       $("#upload-progress").html("Completed");
    }

    var dbCategories = firebase.database().ref("quoteoftheday");

    dbCategories.on("value", function(categories){

        if(categories.exists()){
            var categorieshtml = ""; 
            categories.forEach(function(category){
                
                categorieshtml += "<tr>";

                //for category name
                categorieshtml += "<td>";
                categorieshtml += category.key;
                categorieshtml += "</td>";

                //for category description
                categorieshtml += "<td>";
                categorieshtml += category.val().desc;
                categorieshtml += "</td>";
                
                //for category thumbnail
                categorieshtml += "<td> <img width='250' height='150' src='";
                categorieshtml += category.val().thumbnail;
                categorieshtml += "' /></td>";
                

                categorieshtml += "</tr>";


            });

            $("#categories").html(categorieshtml);
        }

    });

</script>
