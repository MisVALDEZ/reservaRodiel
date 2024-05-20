<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="image/favicon.png" type="image/png">
    <title>Rodriel Tours</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="vendors/linericon/style.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="vendors/nice-select/css/nice-select.css">
    <link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css">
    <!-- main css -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBLdzByXZrnF2iCr6Bq8fcF9fFFi4eEBCI&callback=initMap&libraries=places&v=weekly" defer></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .panel {
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid transparent;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
        }

        .panel-heading {
            padding: 10px 15px;
            border-bottom: 1px solid transparent;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            color: #fff;
        }

        .panel-body {
            padding: 15px;
            color: rgb(41, 43, 44);
            background-color: transparent;
        }

        .panel-danger {
            border-color: #d9534f;
        }

        .panel-danger>.panel-heading {
            background-color: #fc4b08;
            border-color: #fc4b08;
        }
    </style>
    <style type="text/css">
        #map {
            height: 500px;
            width: 800px;
            border-radius: 15px;
            margin: 100px auto;
        }
    </style>
    <script>
        let timer;

        function initMap() {
            const directionsRenderer = new google.maps.DirectionsRenderer();
            const directionsService = new google.maps.DirectionsService();
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 8,
                center: {
                    lat: 18.7357,
                    lng: -70.1627
                },
            });

            directionsRenderer.setMap(map);

            // Llama a la función para calcular y mostrar la ruta cuando la página se carga
            calculateAndDisplayRoute(directionsService, directionsRenderer);

            // Agrega autocompletado a los campos de entrada de origen y destino
            const originInput = document.getElementById('destination');
            const destinationInput = document.getElementById('origin');
            const originAutocomplete = new google.maps.places.Autocomplete(originInput);
            const destinationAutocomplete = new google.maps.places.Autocomplete(destinationInput);

            originAutocomplete.bindTo('bounds', map);
            destinationAutocomplete.bindTo('bounds', map);

            // Agrega eventos de escucha para los cambios en los campos de entrada
            originAutocomplete.addListener('place_changed', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    geocodeAddress(originInput.value, function(coordinates) {
                        calculateAndDisplayRoute(directionsService, directionsRenderer, coordinates);
                    });
                }, 500); // Retraso de 500ms antes de realizar la búsqueda
            });

            destinationAutocomplete.addListener('place_changed', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    geocodeAddress(destinationInput.value, function(coordinates) {
                        calculateAndDisplayRoute(directionsService, directionsRenderer, coordinates);
                    });
                }, 500); // Retraso de 500ms antes de realizar la búsqueda
            });
        }

        function geocodeAddress(address, callback) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                address: address
            }, function(results, status) {
                if (status === "OK") {
                    const location = results[0].geometry.location;
                    const coordinates = {
                        lat: location.lat(),
                        lng: location.lng()
                    };
                    callback(coordinates);
                } else {
                    console.log("Geocode was not successful for the following reason: " + status);
                }
            });
        }

        function calculateAndDisplayRoute(directionsService, directionsRenderer, coordinates) {
            // Obtiene los valores de los inputs de origen y destino
            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;

            // Realiza la solicitud de dirección
            directionsService.route({
                    origin: coordinates || origin,
                    destination: destination,
                    travelMode: google.maps.TravelMode["DRIVING"],
                },
                (response, status) => {
                    if (status == "OK") {
                        directionsRenderer.setDirections(response);
                    } else {
                        //   window.alert("Directions request failed due to " + status);
                    }
                }
            );
        }
    </script>
</head>

<body>
    <!--================Header Area =================-->
    <?php require_once('header.php')  ?>
    <!--================Header Area =================-->

    <!--================Breadcrumb Area =================-->
    <section class="breadcrumb_area">
        <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background=""></div>
        <div class="container">
            <div class="page-cover text-center">
                <h2 class="page-cover-tittle">Reservacion</h2>
                <ol class="breadcrumb">
                    <li><a href="index.html">Home</a></li>
                    <li class="active">Reservacion</li>
                </ol>
            </div>
        </div>
    </section>
    <!--================Breadcrumb Area =================-->

    <!--================ Accomodation Area  =================-->
    <section class="accomodation_area section_gap">
        <div class="container">

        </div>
    </section>
    <!--================ Accomodation Area  =================-->
    <!--================Booking Tabel Area =================-->
    <section class="hotel_booking_area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="">


                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h3 class="text-center">Reservaciones</h3>
                            </div>
                            <div class="panel-body">
                                <form id="reservationForm" class="col-md-10 mx-auto">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="name" id="name" class="form-control" required placeholder="Nombre">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="lastname" id="lastname" class="form-control" required placeholder="Apellido">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="tel" name="phone" id="phone" class="form-control" required placeholder="Número de Teléfono">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="numVuelo" id="numVuelo" class="form-control" placeholder="Número de vuelo">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="destination" id="destination" required class="form-control" placeholder="Destino">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="origin" id="origin" required class="form-control" placeholder="Origen">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <div class='input-group date'>
                                                <input type='text' name="date1" id="datepicker" required class="form-control" placeholder="Fecha de reserva">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class='input-group date'>
                                                <input type="time" name="hour" id="hour" class="form-control" placeholder="Hora">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class='input-group date'>
                                                <input type='number' name="suitcases" id="suitcases" required class="form-control" placeholder="Número de maletas">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="number" name="adults" required id="adults" class="form-control" placeholder="Número de Adultos" min="1">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="number" name="children" id="children" class="form-control" placeholder="Número de Niños (2-12 años)" min="0">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="number" name="infants" id="infants" class="form-control" placeholder="Número de Infantes (0-2 años)" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="book_tabel_item">
                                            <button type="button" id="submitBtn" class="btn btn-danger" style="background-color: #fc4b08">Reservar</button>
                                        </div>
                                    </div>
                                </form>


                            </div>
                        </div>

                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--================Booking Tabel Area  =================-->
    <!--================ Accomodation Area  =================-->
    <section class="accomodation_area section_gap">
        <div class="container">
            <div class="section_title text-center">
                <h2 class="title_color">Trayectoria a Recorrer</h2>
            </div>
            <div class="row">
                <!-- <div class="col-md-12 col-sm-12 col-lg-12"> -->

                <div class="col-md-12 col-sm-12 col-lg-12" id="map"></div>
                <!-- </div> -->

            </div>

        </div>
    </section>
    <!--================ Accomodation Area  =================-->
    <!--================ start footer Area  =================-->
    <?php require_once('footer.php')  ?>
    <!--================ End footer Area  =================-->


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="vendors/owl-carousel/owl.carousel.min.js"></script>
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <script src="vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.js"></script>
    <script src="vendors/nice-select/js/jquery.nice-select.js"></script>
    <script src="js/mail-script.js"></script>
    <script src="js/stellar.js"></script>
    <script src="vendors/lightbox/simpleLightbox.min.js"></script>
    <script src="js/custom.js"></script>
    <script>
        $('#datepicker').datepicker({
            uiLibrary: 'bootstrap4'
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('submitBtn').addEventListener('click', function() {
            var form = document.getElementById('reservationForm');

            // Verificar si el formulario es válido
            if (form.checkValidity() === false) {
                // Mostrar mensajes de error por defecto de HTML5
                form.reportValidity();
                return;
            }

            // Crear objeto FormData y agregar los datos del formulario
            var formData = new FormData(form);

            // Configurar la solicitud AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'process/processReservation.php', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            // Manejadores de respuesta
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        form.reset();
                    } catch (e) {
                        Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: response.error,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        // console.error("No se pudo parsear la respuesta como JSON:", e);
                        // console.error("Respuesta recibida:", xhr.responseText);
                        // alert('Error inesperado. Verifica la consola para más detalles.');
                    }
                } else {
                    Swal.fire({
                            position: "top-end",
                            icon: "error",
                            title: response.error,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    // console.error("Error en la solicitud AJAX:", xhr.status, xhr.statusText);
                    // console.error("Respuesta del servidor:", xhr.responseText);
                    // alert('Error: No se pudo realizar la solicitud.');
                }
            };

            // Enviar la solicitud
            xhr.send(formData);
        });
    </script>
</body>

</html>