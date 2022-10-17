<div class="col-lg-9">
        <h4>All News List</h4>
        <p id="demo"></p>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>id</th>
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

        var database = firebase.database().ref("categories/"+catname);

        database.once("value").then(function(snapshot){
            
            if(snapshot.exists()){
                $("#result").attr("class", "alert alert-danger");
                $("#result").html("Category already exist");
                resetForm();
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

function deleteForm(){
     document.getElementById("demo").innerHTML = "Hello World";
     
    //firebase.database().ref("images").child('/' + key + '/').remove();
    category.key.remove();
    alert('row was removed');
    reload_page();

   }

    var dbCategories = firebase.database().ref("ymg");
     

    dbCategories.on("value", function(categories){

        if(categories.exists()){
            var categorieshtml = ""; 
            categories.forEach(function(category){
                
                categorieshtml += "<tr>";

                //for category name
                categorieshtml += "<td>";
                categorieshtml += category.val().title;
                categorieshtml += "</td>";
                
                //for category thumbnail
                //categorieshtml += "<td> <img width='80' height='50' src='";
                //categorieshtml += category.val().url;
                //categorieshtml += "' /></td>";

                //for category Category
                categorieshtml += "<td>";
                categorieshtml += category.val().category;
                categorieshtml += "</td>";

                //for category Featured
                //categorieshtml += "<td>";
                //categorieshtml += category.val().isFeatured;
                //categorieshtml += "</td>";

                //for category Action
                categorieshtml += "<td>"
                categorieshtml += category.val().id;
                categorieshtml += "</td>";
                

                categorieshtml += "</tr>";


            });

            $("#categories").html(categorieshtml);
        }

    });

$(function () {
    $("#4").click(function (category) {
        //e.preventDefault();
        var category = firebase.database().ref("images");
        category.key.remove();
        alert(category.key);
    });
});



</script>

