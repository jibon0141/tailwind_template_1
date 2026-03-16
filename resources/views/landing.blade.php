<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tahsin Pharma</title>


    <script src="{{ asset('assets/backend_assets/js/tailwind/tailwind.js') }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body>

<!-- Header -->
<header id="mainHeader" class="md:sticky md:top-0 md:z-50">

    <div id="headerInner"
         class="mx-6 px-8 py-3 flex flex-col justify-center items-center text-center
                md:flex-row md:justify-between
                bg-slate-300 text-black rounded-xl
                transition-all duration-300">

        <div class="flex flex-col md:flex-row items-center gap-2 justify-center w-full">
            <img class="h-6 w-6 mix-blend-multiply"
                 src="{{ '/image/company_logo/'.$company->logo }}"
                 alt="Logo">

            <button class="text-xl font-bold">
                Tahsin Pharma
            </button>
        </div>

        <div class="flex justify-center items-center w-full">
            <ul class="flex flex-col md:flex-row gap-5 p-2">
                <li  class="flex-shrink-0"><a href="/" class="font-extrabold">Home</a></li>
                <li  class="flex-shrink-0"><a href="#about_us">About Us</a></li>
                <li  class="flex-shrink-0"><a href="#product_section">Products</a></li>
                <li  class="flex-shrink-0"><a href="#service_section">Services</a></li>
                <li  class="flex-shrink-0"><a href="#investor_relations">Investor Relations</a></li>
            </ul>
        </div>

    </div>
</header>

<!-- Slider Section -->
<div class="mx-6 mt-4 rounded-xl overflow-hidden relative">

    <div class="slider relative w-full h-64 md:h-[600px]">

        <div class="slide absolute w-full h-full transition-opacity duration-1000">
            <img src="/image/slider/slider4.jpg"
                 class="w-full h-full object-cover rounded-xl"
                 alt="Slide 1">
        </div>

        <div class="slide absolute w-full h-full opacity-0 transition-opacity duration-1000">
            <img src="/image/slider/slider5.jpg"
                 class="w-full h-full object-cover rounded-xl"
                 alt="Slide 2">
        </div>

        <div class="slide absolute w-full h-full opacity-0 transition-opacity duration-1000">
            <img src="/image/slider/slider3.jpg"
                 class="w-full h-full object-cover rounded-xl"
                 alt="Slide 3">
        </div>

    </div>
</div>

<!-- Main Section -->
<main class="mx-6 mt-6">


    <!-- About Us Section -->
    <section class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-2xl shadow-md p-8 mb-12 scroll-mt-28" id="about_us" >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">

            <!-- Left Content -->
            <div >
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    About Tahsin Pharma
                </h1>

                <p class="text-gray-600 mb-4 leading-relaxed text-justify">
                    Tahsin Pharma is a trusted and growing pharmaceutical company committed to delivering
                    high-quality medicines and healthcare products across the country. With a strong focus
                    on quality assurance, ethical business practices, and reliable distribution, we aim
                    to improve healthcare accessibility for all.
                </p>

                <p class="text-gray-600 mb-4 leading-relaxed text-justify">
                    Our expertise includes pharmaceutical product distribution, wholesale and retail
                    medicine supply, import & export operations, and strategic supplier management.
                    We ensure that every product meets strict safety and regulatory standards before
                    reaching our partners and customers.
                </p>

                <p class="text-gray-600 leading-relaxed text-justify">
                    At Tahsin Pharma, innovation, integrity, and customer satisfaction are at the heart
                    of everything we do. Our mission is to contribute to a healthier future by providing
                    safe, affordable, and effective pharmaceutical solutions.
                </p>

                <!-- Optional Button -->
                <div class="mt-6">
                    <a href="#service_section"
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-lg transition duration-300">
                        Explore Our Services
                    </a>
                </div>
            </div>

            <!-- Right Image -->
            <div>
                <img src="/image/service_landing_image/service1.jpg"
                     class="w-full h-80 object-cover rounded-2xl shadow-lg"
                     alt="Tahsin Pharma">
            </div>

        </div>
    </section>


    <!-- Product Section Start -->
    <h1 id="product_section" class="text-2xl font-bold text-center scroll-mt-28">
        Products
    </h1>

    <section class="py-10 grid grid-cols-2 md:grid-cols-4 auto-rows-[200px] gap-5">

        <!-- Big Item -->
        <img src="/image/product_landing_image/product1.jpg"
             class="w-full h-full object-cover rounded-xl transform transition duration-300 ease-in-out hover:scale-105"
             alt="">

        <!-- Normal -->
        <img src="/image/product_landing_image/product2.jpg"
             class="w-full h-full object-cover rounded-xl transform transition duration-300 ease-in-out hover:scale-105"
             alt="">

        <!-- Tall -->
        <img src="/image/product_landing_image/product3.jpg"
             class=" w-full h-full object-cover rounded-xl transform transition duration-300 ease-in-out hover:scale-105"
             alt="">

        <!-- Wide -->
        <img src="/image/product_landing_image/product4.jpg"
             class="row-span-2 w-full h-full object-cover rounded-xl transform transition duration-300 ease-in-out hover:scale-105"
             alt="">

        <!-- Normal -->
        <img src="/image/product_landing_image/product5.jpg"
             class="w-full h-full object-cover rounded-xl transform transition duration-300 ease-in-out hover:scale-105"
             alt="">
        <img src="/image/product_landing_image/product6.webp"
             class="w-full h-full object-cover rounded-xl transform transition duration-300 ease-in-out hover:scale-105"
             alt="">
        <img src="/image/product_landing_image/product7.webp"
             class="w-full h-full object-cover rounded-xl transform transition duration-300 ease-in-out hover:scale-105"
             alt="">

    </section>

    <!-- Service Section Start -->
    <h1 id="service_section" class="text-2xl font-bold text-center scroll-mt-28">Our Services</h1>
    <section class="py-10 rounded-xl grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

        <!-- Card 1 -->
        <div class="bg-slate-200 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 p-5 text-center">
            <img src="/image/service_landing_image/service1.jpg"
                 class="w-4/5 h-48 mx-auto object-cover rounded-xl mb-4"
                 alt="Service">

            <h3 class="font-semibold text-lg mb-2">
                Pharmaceutical Product Distribution
            </h3>

            <p class="text-sm text-gray-600">
                Reliable nationwide distribution with strict quality assurance.
            </p>
        </div>

        <!-- Card 2 -->
        <div class="bg-slate-200 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 p-5 text-center">
            <img src="/image/service_landing_image/service2.jpg"
                 class="w-4/5 h-48 mx-auto object-cover rounded-xl mb-4"
                 alt="Service">

            <h3 class="font-semibold text-lg mb-2">
                Wholesale & Retail Medicine Supply
            </h3>

            <p class="text-sm text-gray-600">
                Supplying medicines efficiently to pharmacies and healthcare centers.
            </p>
        </div>

        <!-- Card 3 -->
        <div class="bg-slate-200 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 p-5 text-center">
            <img src="/image/service_landing_image/service3.jpg"
                 class="w-4/5 h-48 mx-auto object-cover rounded-xl mb-4"
                 alt="Service">

            <h3 class="font-semibold text-lg mb-2">
                Import & Export of Pharmaceutical Products
            </h3>

            <p class="text-sm text-gray-600">
                International sourcing and export of certified pharmaceutical goods.
            </p>
        </div>

        <!-- Card 4 -->
        <div class="bg-slate-200 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 p-5 text-center">
            <img src="/image/service_landing_image/service4.jpg"
                 class="w-4/5 h-48 mx-auto object-cover rounded-xl mb-4"
                 alt="Service">

            <h3 class="font-semibold text-lg mb-2">
                Procurement & Supplier Management
            </h3>

            <p class="text-sm text-gray-600">
                Strategic sourcing and trusted supplier partnerships.
            </p>
        </div>

        <!-- Card 5 -->
        <div class="bg-slate-200 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 p-5 text-center">
            <img src="/image/service_landing_image/service5.jpg"
                 class="w-4/5 h-48 mx-auto object-cover rounded-xl mb-4"
                 alt="Service">

            <h3 class="font-semibold text-lg mb-2">
                Inventory & Stock Management
            </h3>

            <p class="text-sm text-gray-600">
                Smart inventory tracking and optimized stock control systems.
            </p>
        </div>

        <!-- Card 6 -->
        <div class="bg-slate-200 rounded-2xl shadow-lg hover:shadow-2xl transition duration-300 p-5 text-center">
            <img src="/image/service_landing_image/service6.jpg"
                 class="w-4/5 h-48 mx-auto object-cover rounded-xl mb-4"
                 alt="Service">

            <h3 class="font-semibold text-lg mb-2">
                Quality-Controlled Medicine Supply
            </h3>

            <p class="text-sm text-gray-600">
                Ensuring safe, authentic, and high-quality medicine distribution.
            </p>
        </div>

    </section>

    <!-- Investor Relations -->
    <section id="investor_relations"
             class="scroll-mt-32 bg-gradient-to-r from-slate-50 to-white rounded-2xl shadow-md p-8 mb-12">

        <div class="max-w-6xl mx-auto">

            <!-- Section Title -->
            <div class="text-center mb-10">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">
                    Investor Relations
                </h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Tahsin Pharma is committed to transparency, sustainable growth, and long-term value
                    creation for our investors and stakeholders.
                </p>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Company Overview -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition duration-300">
                    <h3 class="text-xl font-semibold mb-3">Company Overview</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        A growing pharmaceutical distribution company focused on nationwide
                        medicine supply, ethical operations, and strategic partnerships.
                    </p>
{{--                    <a href="#"--}}
{{--                       class="text-blue-600 font-semibold hover:underline text-sm">--}}
{{--                        Learn More →--}}
{{--                    </a>--}}
                </div>

                <!-- Financial Highlights -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition duration-300">
                    <h3 class="text-xl font-semibold mb-3">Financial Highlights</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>✔ Consistent annual revenue growth</li>
                        <li>✔ Expanding distribution network</li>
                        <li>✔ Strong supplier partnerships</li>
                        <li>✔ Sustainable business strategy</li>
                    </ul>
                </div>

                <!-- Investor Contact -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition duration-300">
                    <h3 class="text-xl font-semibold mb-3">Investor Contact</h3>
                    <p class="text-gray-600 text-sm mb-3">
                        For investment inquiries, partnership opportunities, or financial
                        information requests, please contact:
                    </p>
                    <p class="text-sm font-medium">Email: {{$company->email}}</p>
                    <p class="text-sm font-medium">Phone: +88 {{$company->phone}}</p>
                </div>

            </div>

            <!-- Call To Action -->
{{--            <div class="text-center mt-10">--}}
{{--                <a href="#contact_section"--}}
{{--                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl shadow-lg transition duration-300">--}}
{{--                    Become an Investor--}}
{{--                </a>--}}
{{--            </div>--}}

        </div>
    </section>


</main>



<!--Footer Section-->
<footer class="mx-6 rounded-lg flex flex-col bg-slate-300 text-black px-6 py-5 md:px-8 md:mt-5">
    <div id="contact_section" class="flex flex-col items-center justify-center md:flex-col mb-6">
        <img
            class="h-14 w-14 mb-2 md:mb-2 mix-blend-multiply"
            src="{{ '/image/company_logo/'.$company->logo }}"
            alt="Logo"
        />
        <h1 class="text-xl text-center  font-bold">Tahsin Pharma</h1>
    </div>

    <div class="flex flex-col gap-y-5 justify-center items-center
            md:flex-row md:items-start">

        <!-- Contact -->
        <div class="flex flex-col gap-y-2 items-center md:items-start">
            <h1 class="font-semibold">Contact</h1>
            <p class="text-sm max-w-xs break-words">
                {{$company->address}}
            </p>
            <p class="text-sm max-w-xs break-words">Contact:{{$company->phone}}</p>
            <p class="text-sm">Email: {{$company->email}}</p>
            <p class="text-sm">Time Of Visit: 9.00 AM - 10.00 PM</p>
        </div>

        <!-- Services -->
        <div class="flex flex-col gap-y-2 items-center md:items-start">
            <h1 class="font-semibold">Our Services</h1>

            <p class="text-sm">Pharmaceutical Product Distribution</p>
            <p class="text-sm">Wholesale & Retail Medicine Supply</p>
            <p class="text-sm">Import & Export of Pharmaceutical Products</p>
            <p class="text-sm">Procurement & Supplier Management</p>
            <p class="text-sm">Inventory & Stock Management</p>
            <p class="text-sm">Quality-Controlled Medicine Supply &amp; more</p>
        </div>

    </div>
    <div class="mt-6 flex flex-wrap justify-center gap-4">
        <a href="https://www.facebook.com/">
            <img class="w-8 h-8" src="https://img.icons8.com/?size=48&amp;id=118497&amp;format=png" alt="Facebook">
        </a>
        <a href="https://x.com/i/flow/login">
            <img class="w-8 h-8" src="https://img.icons8.com/?size=48&amp;id=13963&amp;format=png" alt="Twitter">
        </a>
        <a href="https://www.youtube.com/">
            <img class="w-8 h-8" src="https://img.icons8.com/?size=48&amp;id=19318&amp;format=png" alt="YouTube">
        </a>
        <a href="https://www.instagram.com/">
            <img class="w-8 h-8" src="https://img.icons8.com/?size=48&amp;id=Xy10Jcu1L2Su&amp;format=png" alt="Instagram">
        </a>
    </div>
    <div class=" text-center mt-6">
        <p class="text-sm font-semibold">©2026 Easy It Solution. All Rights Reserved.
        </p>
    </div>
</footer>

</body>
</html>

<script>
    const slides = document.querySelectorAll(".slide");
    let current = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle("opacity-0", i !== index);
            slide.classList.toggle("opacity-100", i === index);
        });
    }

    function nextSlide() {
        current = (current + 1) % slides.length;
        showSlide(current);
    }


    setInterval(nextSlide, 4000);

    // Show first slide initially
    showSlide(current);
</script>


<script>
    const headerInner = document.getElementById("headerInner");

    function handleScrollEffect() {


        if (window.innerWidth < 768) return;

        if (window.scrollY > 50) {

            headerInner.classList.remove("bg-slate-300");
            headerInner.classList.add("bg-white/30", "backdrop-blur-md", "shadow-lg");

        } else {

            headerInner.classList.remove("bg-white/30", "backdrop-blur-md", "shadow-lg");
            headerInner.classList.add("bg-slate-300");

        }
    }

    window.addEventListener("scroll", handleScrollEffect);
    window.addEventListener("resize", handleScrollEffect);

    handleScrollEffect();
</script>
