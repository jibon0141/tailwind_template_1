@extends("admin.master")
@section("content")

    <div class="mt-8 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Send Feedback</h2>
        <form action="#" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col">
                    <label for="name" class="mb-2 text-sm font-medium text-gray-600">Name</label>
                    <input type="text" id="name" name="name"
                           class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                           placeholder="Your Name">
                </div>
                <div class="flex flex-col">
                    <label for="email" class="mb-2 text-sm font-medium text-gray-600">Email</label>
                    <input type="email" id="email" name="email"
                           class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                           placeholder="Your Email">
                </div>
            </div>
            <div class="mt-6 flex flex-col">
                <label for="message" class="mb-2 text-sm font-medium text-gray-600">Message</label>
                <textarea id="message" name="message" rows="4"
                          class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                          placeholder="Your Message"></textarea>
            </div>
            <div class="mt-6 text-right">
                <button type="submit"
                        class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-6 rounded transition duration-200">
                    Submit
                </button>
            </div>
        </form>
    </div>



@endsection
