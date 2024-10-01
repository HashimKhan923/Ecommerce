<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>DAM | Auto Marketplace</title>
    <meta
      name="keywords"
      content="auto parts, car modifications, vehicle customization, aftermarket parts, performance parts"
    />
    <meta name="author" content="DAM Team" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp,container-queries"></script>
    <!-- <link rel="stylesheet" href="index.css" /> -->
    <style>
      .hero {
        width: 100%;
        height: 100%;
        background-image: url("https://devapi.dragonautomart.com/EmailTemplate/hero.jpeg");
        background-size: cover; /* Ensure the image covers the entire modal */
        background-repeat: no-repeat; /* Prevent the image from repeating */
        background-position: center;
      }
    </style>
  </head>
  <body class="max-w-[50rem] mx-auto">
    <!-- header  -->
    <header
      class="bg-[#4d4d4d] px-8 w-full flex flex-wrap items-center justify-between"
    >
      <!-- logo  -->
      <div class="flex items-center justify-center gap-x-2 flex-shrink-0">
        <a target="_blank" href="https://dragonautomart.com">
          <img
            class="size-28 object-contain"
            src="https://devapi.dragonautomart.com/EmailTemplate/logo.png"
            alt="logo"
          />
        </a>
        <a target="_blank" href="https://dragonautomart.com">
          <img
            class="size-32 object-contain"
            src="https://devapi.dragonautomart.com/EmailTemplate/whitetext.png"
            alt="logo"
          />
        </a>
      </div>
      <!-- right side options  -->
      <div
        class="flex items-center justify-center gap-x-6 text-white tracking-wider"
      >
        <a target="_blank" href="https://dragonautomart.com/allmakes">
          Popular Makes
        </a>
        <a target="_blank" href="https://dragonautomart.com/sellondam">Sell</a>
        <a target="_blank" href="https://dragonautomart.com">
          <button
            class="bg-red-600 rounded-lg px-5 py-2 text-white font-medium text-md uppercase"
          >
            Explore More
          </button>
        </a>
      </div>
    </header>

    <!-- hero section  -->
    <div class="hero h-screen w-full bg-black px-4">
      <!-- heading section -->
      <div class="pt-6">
        <div
          class="font-normal text-white uppercase text-[1.5rem] drop-shadow-xl"
        >
          <span class="[text-shadow:_0_2px_0_rgb(0_0_0_/_50%)]">
            new arrivals
          </span>
        </div>
        <div
          class="font-semibold text-white text-[3rem] mb-2 -mt-2 drop-shadow-2xl"
        >
          <span class="[text-shadow:_0_2px_0_rgb(0_0_0_/_80%)]">
            Save up to 30% on all items.
          </span>
        </div>
        <a target="_blank" href="https://dragonautomart.com">
          <button
            class="bg-red-600 drop-shadow-xl shadow-xl rounded-lg px-6 py-2 text-white tracking-wider font-semibold text-lg"
          >
            Shop Now
          </button>
        </a>
      </div>
    </div>
    <!-- diffuser  -->
    <div class="bg-gradient-to-b from-transparent to-white -mt-64 h-64"></div>
    <!-- categories grid boxes  -->
    <div class="flex items-center justify-center gap-5 my-2 mx-2 -mt-32">
      <!-- left vertical grid  -->
      <div class="w-[40%]">
        <div
          class="flex flex-col items-center justify-center p-3 gap-y-5 bg-gray-200 border border-gray-400 rounded-xl"
        >
          <!-- category box  -->
          <a
            target="_blank"
            href="https://dragonautomart.com/subcategory/Tail%20Lights/4"
          >
            <div class="flex flex-col items-center justify-center">
              <img
                class="w-64 h-36 object-contain"
                src="https://devapi.dragonautomart.com/EmailTemplate/tail.png"
                alt="logo"
              />
              <span class="font-medium text-lg"> Tail Lights </span>
            </div>
          </a>
          <!-- category box  -->
          <a
            target="_blank"
            href="https://dragonautomart.com/subcategory/Headlights/5"
          >
            <div class="flex flex-col items-center justify-center">
              <img
                class="w-64 h-36 object-contain"
                src="https://devapi.dragonautomart.com/EmailTemplate/head.png"
                alt="logo"
              />
              <span class="font-medium text-lg"> Headlights </span>
            </div>
          </a>
        </div>
      </div>
      <!-- right horizontal grid  -->
      <div class="w-[60%] flex flex-col items-center justify-center gap-y-3">
        <div
          class="flex items-center justify-center p-5 gap-x-5 bg-gray-200 border border-gray-400 rounded-xl w-full"
        >
          <!-- category box  -->
          <a
            target="_blank"
            href="https://dragonautomart.com/subcategory/Bumpers/66"
          >
            <div class="flex flex-col items-center justify-center">
              <img
                class="w-48 h-full object-contain"
                src="https://devapi.dragonautomart.com/EmailTemplate/bumper.png"
                alt="logo"
              />
              <span class="font-medium text-lg">Bumper </span>
            </div>
          </a>
          <!-- category box  -->
          <a
            target="_blank"
            href="https://dragonautomart.com/subcategory/Fenders/67"
          >
            <div class="flex flex-col items-center justify-center">
              <img
                class="w-48 h-full object-contain"
                src="https://devapi.dragonautomart.com/EmailTemplate/fender.png"
                alt="logo"
              />
              <span class="font-medium text-lg"> Fenders </span>
            </div>
          </a>
        </div>
        <div
          class="flex items-center justify-center p-5 gap-x-5 bg-gray-200 border border-gray-400 rounded-xl w-full"
        >
          <!-- category box  -->
          <a
            target="_blank"
            href="https://dragonautomart.com/subcategory/Wheels/112"
          >
            <div class="flex flex-col items-center justify-center">
              <img
                class="w-48 h-full object-contain"
                src="https://devapi.dragonautomart.com/EmailTemplate/rims.png"
                alt="logo"
              />
              <span class="font-medium text-lg"> Wheels </span>
            </div>
          </a>
          <!-- category box  -->
          <a
            target="_blank"
            href="https://dragonautomart.com/category/Brakes/18"
          >
            <div class="flex flex-col items-center justify-center">
              <img
                class="w-48 h-[124px] object-contain"
                src="https://devapi.dragonautomart.com/EmailTemplate/brake.png"
                alt="logo"
              />
              <span class="font-medium text-lg"> Brakes </span>
            </div>
          </a>
        </div>
      </div>
    </div>
    <!-- round makes  -->
    <div
      class="flex items-center justify-center gap-x-3 p-3 bg-[#F6F6F6] rounded-xl border border-gray-400 mb-2 mx-2"
    >
      <!-- make card  -->
      <a href="https://dragonautomart.com/make/Toyota/1">
        <div className="flex flex-col items-center justify-center gap-y-2">
          <img
            class="p-2.5 h-[7rem] w-[7rem] object-cover rounded-full bg-white border border-gray-300"
            src="https://api.dragonautomart.com/Brand/20231207125747bAY5J4xfVzBSEOq2PCCw7vsrOIkXckiM7AnQPuGS.png"
          />
          <div class="font-medium text-sm text-center">Toyota</div>
        </div>
      </a>
      <!-- make card  -->
      <a href="https://dragonautomart.com/make/Honda/3">
        <div className="flex flex-col items-center justify-center gap-y-2">
          <img
            class="p-2.5 h-[7rem] w-[7rem] object-cover rounded-full bg-white border border-gray-300"
            src="https://api.dragonautomart.com/Brand/20231207125809xAQikskHMre64dR1wl9PXnOlUaoyhP2yUYWIz6sE.png"
          />
          <div class="font-medium text-sm text-center">Honda</div>
        </div>
      </a>

      <a href="https://dragonautomart.com/make/Ford/7">
        <!-- make card  -->
        <div className="flex flex-col items-center justify-center gap-y-2">
          <img
            class="p-2.5 h-[7rem] w-[7rem] object-cover rounded-full bg-white border border-gray-300"
            src="https://api.dragonautomart.com/Brand/20231207125946PYfzlfpPczxOtX5NZee5w2ImThjGGkRzysVfnf8m.png"
          />
          <div class="font-medium text-sm text-center">Ford</div>
        </div>
      </a>
      <!-- make card  -->
      <a href="https://dragonautomart.com/make/Tesla/72">
        <div className="flex flex-col items-center justify-center gap-y-2">
          <img
            class="p-2.5 h-[7rem] w-[7rem] object-cover rounded-full bg-white border border-gray-300"
            src="https://api.dragonautomart.com/Brand/20240301152912tesla.png"
          />
          <div class="font-medium text-sm text-center">Tesla</div>
        </div>
      </a>

      <!-- make card  -->
      <a href="https://dragonautomart.com/make/BMW/5">
        <div className="flex flex-col items-center justify-center gap-y-2">
          <img
            class="p-2.5 h-[7rem] w-[7rem] object-cover rounded-full bg-white border border-gray-300"
            src="https://api.dragonautomart.com/Brand/20240301145134bmw.png"
          />
          <div class="font-medium text-sm text-center">BMW</div>
        </div>
      </a>

      <!-- make card  -->
      <a href="https://dragonautomart.com/make/Audi/20">
        <div className="flex flex-col items-center justify-center gap-y-2">
          <img
            class="p-2.5 h-[7rem] w-[7rem] object-cover rounded-full bg-white border border-gray-300"
            src="https://api.dragonautomart.com/Brand/20240301153950audi.png"
          />
          <div class="font-medium text-sm text-center">Audi</div>
        </div>
      </a>
    </div>
    <!-- buyer section  -->
    <div
      class="flex items-center justify-center gap-x-3 p-3 bg-gray-100 rounded-xl border border-gray-400 mb-2 mx-2"
    >
      <img
        class="w-80 h-full object-cover rounded-xl"
        src="https://devapi.dragonautomart.com/EmailTemplate/customer_faq.jpeg"
        alt="logo"
      />
      <div class="flex flex-col items-start justify-start gap-y-2">
        <div class="text-green-900 font-semibold text-3xl">
          Are you a Buyer?
        </div>
        <div class="text-black text-sm tracking-wide font-normal">
          For automotive aficionados seeking clarity on our platform, whether
          through our dedicated mobile app or website, we strive to provide
          comprehensive responses, addressing your specific inquiries and
          concerns with precision and care.
        </div>
        <a target="_blank" href="https://dragonautomart.com/faq">
          <button
            class="bg-green-800 drop-shadow-sm shadow-sm rounded-full px-10 py-1.5 text-white tracking-wider font-semibold text-sm"
          >
            Explore
          </button>
        </a>
      </div>
    </div>
    <!-- seller section  -->
    <div
      class="flex items-center justify-center gap-x-3 p-3 bg-gray-100 rounded-xl border border-gray-400 mb-2 mx-2"
    >
      <div class="flex flex-col items-start justify-start gap-y-2">
        <div class="text-red-600 font-semibold text-3xl">
          Want to be a Seller?
        </div>
        <div class="text-black text-sm tracking-wide font-normal">
          For sellers eager to join our platform and maximize their sales
          potential, whether it's understanding the selling process, managing
          payouts, processing orders, or shipping procedures, we offer detailed
          guidance and comprehensive FAQs.
        </div>
        <a target="_blank" href="https://dragonautomart.com/sellondam">
          <button
            class="bg-red-600 drop-shadow-sm shadow-sm rounded-full px-10 py-1.5 text-white tracking-wider font-semibold text-sm"
          >
            Explore
          </button>
        </a>
      </div>
      <img
        class="w-80 h-full object-cover rounded-xl"
        src="https://devapi.dragonautomart.com/EmailTemplate/seller_faq.jpeg"
        alt="logo"
      />
    </div>

    <!-- exclusive products  -->
    <!-- heading  -->
    <div
      class="text-3xl text-black font-semibold tracking-wide uppercase w-full text-center my-4"
    >
      Exclusive Products
    </div>
    <!-- 3 product cards  -->
    <div class="flex items-center justify-center gap-x-[13px] mx-2">
      <!-- product card  -->
      <a target="_blank" href="https://dragonautomart.com/product/503">
        <div
          class="flex flex-col items-center justify-center gap-y-1 w-[16rem]"
        >
          <img
            class="rounded-lg object-contain"
            src="https://api.dragonautomart.com/ProductGallery/20240220150256HRS-2018-22HondaAccordLEDTailLightsOEStyle-V5-4.jpg.webp"
          />
          <div class="font-light text-sm text-center text-green-800">
            Hirev Sports
          </div>
          <div class="font-normal text-[15px] text-center">
            Honda Accord LED Tail Lights OE Style - V5
          </div>
        </div>
      </a>
      <!-- product card  -->
      <a target="_blank" href="https://dragonautomart.com/product/5467">
        <div
          class="flex flex-col items-center justify-center gap-y-1 w-[16rem]"
        >
          <img
            class="rounded-lg object-contain"
            src="https://www.vicrez.com/image/catalog/vicrez-wheels/redeye-demon-style-widebody-matte-black-wheel-vzn102593.jpg"
          />
          <div class="font-light text-sm text-center text-green-800">
            Vicrez
          </div>
          <div class="font-normal text-[15px] text-center">
            Redeye Demon Style Matte Black Wheel 20" x 10.5"
          </div>
        </div>
      </a>
      <!-- product card  -->
      <a target="_blank" href="https://dragonautomart.com/product/522">
        <div
          class="flex flex-col items-center justify-center gap-y-1 w-[16rem]"
        >
          <img
            class="rounded-lg object-contain"
            src="https://api.dragonautomart.com/ProductGallery/20240221142955HRS-2018-23ToyotaCamryAvalonCarbonFiberSteeringWheel-4.jpg.webp"
          />
          <div class="font-light text-sm text-center text-green-800">
            Hirev Sports
          </div>
          <div class="font-normal text-[15px] text-center">
            Toyota Camry/Avalon Carbon Fiber Steering Wheel
          </div>
        </div>
      </a>
    </div>
    <br />
    <!-- cross grid section  -->
    <div class="flex items-center justify-center">
      <div class="w-[50%]">
        <img
          class="w-full h-full object-cover"
          src="https://api.dragonautomart.com/ProductGallery/20240811203025_758b7cc0-acd1-4591-95d5-191d63336368.webp"
          alt="logo"
        />
      </div>
      <div
        class="w-[50%] flex flex-col items-center justify-center gap-y-2 px-3"
      >
        <div class="text-black font-semibold text-3xl">
          Ford Super Duty Headlights
        </div>
        <div class="text-black text-center text-sm tracking-wide font-normal">
          Introducing Morimoto XB LED Headlights for Ford Super Duty trucks.
        </div>
        <a
          target="_blank"
          href="https://dragonautomart.com/searchresults/ford%20headlight"
        >
          <button
            class="bg-red-600 drop-shadow-sm shadow-sm rounded-full px-10 py-1.5 text-white tracking-wider font-semibold text-sm"
          >
            Shop Now
          </button>
        </a>
      </div>
    </div>
    <div class="flex items-center justify-center mb-1">
      <div
        class="w-[50%] flex flex-col items-center justify-center gap-y-2 px-3"
      >
        <div class="text-black font-semibold text-3xl">
          Toyota Camry Tail lights
        </div>
        <div class="text-black text-sm tracking-wide font-normal">
          Introducing Toyota Camry Lexus Style LED Tail Lights.
        </div>
        <a
          target="_blank"
          href="https://dragonautomart.com/searchresults/toyota%20camry%20tail%20light"
        >
          <button
            class="bg-red-600 drop-shadow-sm shadow-sm rounded-full px-10 py-1.5 text-white tracking-wider font-semibold text-sm"
          >
            Shop Now
          </button>
        </a>
      </div>
      <div class="w-[50%]">
        <img
          class="w-full h-full object-cover"
          src="https://api.dragonautomart.com/ProductGallery/202402211530592018-Toyota-Camry-Tail-Lights-lexus_Style-BWB.jpg.webp"
          alt="logo"
        />
      </div>
    </div>
    <!-- footer  -->
    <footer class="flex items-center justify-between px-8 bg-[#4d4d4d] w-full">
      <img
        class="w-32 h-20 object-contain"
        src="https://devapi.dragonautomart.com/EmailTemplate/whitetext.png"
        alt="logo"
      />
      <!-- sm icons -->
      <div
        class="flex items-center md:justify-start justify-between gap-x-2 pt-1 pb-3 md:pb-0"
      >
        <a
          target="_blank"
          href="https://web.facebook.com/dragonautomart?_rdc=1&_rdr"
          class="p-2 rounded-full bg-[#2d2d2d]"
        >
          <img
            class="w-5"
            src="https://devapi.dragonautomart.com/EmailTemplate/footerfacebook.webp"
            alt="facebook"
          />
        </a>
        <a
          target="_blank"
          href="https://www.instagram.com/dragonautomart/"
          class="p-2 rounded-full bg-[#2d2d2d]"
        >
          <img
            class="w-5"
            src="https://devapi.dragonautomart.com/EmailTemplate/footerinsta.webp"
            alt="insta"
          />
        </a>
        <a
          target="_blank"
          href="https://www.X.com/dragonautomart"
          class="p-2 rounded-full bg-[#2d2d2d]"
        >
          <img
            class="w-5"
            src="https://devapi.dragonautomart.com/EmailTemplate/footerx.webp"
            alt="x"
          />
        </a>
        <a
          target="_blank"
          href="https://www.youtube.com/@dragonautomart"
          class="p-2 rounded-full bg-[#2d2d2d]"
        >
          <img
            class="w-5"
            src="https://devapi.dragonautomart.com/EmailTemplate/footeryt.webp"
            alt="youtube"
          />
        </a>
        <a
          target="_blank"
          href="https://tiktok.com/@dragonautomart"
          class="p-2 rounded-full bg-[#2d2d2d]"
        >
          <img
            class="w-5"
            src="https://devapi.dragonautomart.com/EmailTemplate/footertiktok.webp"
            alt="tiktok"
          />
        </a>
      </div>
    </footer>
    <!-- last line footer -->
    <footer
      class="flex items-center justify-between px-8 bg-[#4d4d4d] w-full border-t-[0.5px] border-gray-200"
    >
      <div class="text-center text-white text-sm flex items-center">
        Powered By
        <a
          href="https://hirevsports.com"
          target="_blank"
          class="hover:scale-95 transition-all duration-200"
        >
          <img
            class="w-14 h-14 object-contain"
            src="https://dragonautomart.com/assets/HRS_LOGO-ab8211d0.png"
            alt="hirevsports"
          />
        </a>
      </div>
      <div class="text-center text-white tracking-wider text-xs">
        © 2024 Dragon Auto Mart, All Rights Reserved.
      </div>
      <div class="w-32"></div>
    </footer>
  </body>
</html>
