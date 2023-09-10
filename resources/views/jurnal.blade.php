@extends('app') <!-- Menggunakan layout 'app.blade.php' -->

@section('Jurnal', 'Halaman Jurnal') <!-- Judul halaman -->

@section('content')
    <!-- Konten jurnal Anda akan dimasukkan di sini -->
    <div class="container mt-5">
        <form class="w-100 d-flex justify-content-center align-items-center" id="search-form">
            {{-- <label for="search">Search &nbsp;</label> --}}
            <input type="text" name="search" id="search" class="border border-none rounded-pill shadow-lg px-3 py-2 w-50"
                placeholder="Search Jurnal" />
            <button type="submit" class="btn-search border px-3 py-2 rounded-pill m-1">Search</button>
        </form>

        <div class="text-center mt-3" id="word-list">

        </div>
        <script>
            // $(document).ready(function() {
            //     $.ajax({
            //         url: '/get-word',
            //         method: 'GET',
            //         success: function(response) {
            //             var wordFrequency = JSON.parse(response.wordFrequency);

            //             // Mendapatkan daftar kata-kata dan total kemunculan
            //             var wordList = [];

            //             for (var word in wordFrequency) {
            //                 wordList.push({
            //                     word: word,
            //                     count: wordFrequency[word]
            //                 });
            //             }

            //             // Mengurutkan daftar kata-kata berdasarkan total kemunculan
            //             wordList.sort(function(a, b) {
            //                 return b.count - a.count;
            //             });

            //             // Menampilkan data kata dan total kemunculannya
            //             var wordListHtml = '';

            //             for (var i = 0; i < wordList.length; i++) {
            //                 wordListHtml +=
            //                     '<button class="border px-3 py-2 rounded-pill m-1" data-search-btn="' +
            //                     wordList[i].word + '">' + wordList[i].word + '' + wordList[i].count +
            //                     '</button>';
            //             }

            //             // Menambahkan data ke elemen dengan id 'word-list'
            //             $('#word-list').append(wordListHtml);
            //         },
            //         error: function(error) {
            //             console.error('Gagal mengambil data word frequency:', error);
            //         }
            //     });
            // });
            $(document).ready(function() {
                $.ajax({
                    url: '/get-word',
                    method: 'GET',
                    success: function(response) {
                        var wordFrequency = JSON.parse(response.wordFrequency);

                        // Mendapatkan daftar kata-kata dan total kemunculan
                        var wordList = [];

                        for (var word in wordFrequency) {
                            wordList.push({
                                word: word,
                                count: wordFrequency[word]
                            });
                        }

                        // Mengurutkan daftar kata-kata berdasarkan total kemunculan
                        wordList.sort(function(a, b) {
                            return b.count - a.count;
                        });

                        // Menampilkan data kata dan total kemunculannya
                        var wordListHtml = '';

                        for (var i = 0; i < wordList.length; i++) {
                            wordListHtml +=
                                '<button class="px-3 py-2 rounded-pill m-1 btn-trend" data-search-btn="' +
                                wordList[i].word + '">' + wordList[i].word + '&nbsp;&nbsp;' + wordList[i]
                                .count +
                                '</button>';
                        }

                        // Menambahkan data ke elemen dengan id 'word-list'
                        $('#word-list').append(wordListHtml);

                        // Attach a click event handler to the buttons
                        $('[data-search-btn]').click(function() {
                            var word = $(this).data('search-btn');
                            $('#search').val(word); // Set the search input value
                            $('#search-form').submit(); // Trigger the search form submission
                        });
                    },
                    error: function(error) {
                        console.error('Gagal mengambil data word frequency:', error);
                    }
                });
            });
        </script>

    </div>
    <div class="container d-flex flex-wrap mt-5 gap-3" id="data-container">

    </div>
    <div id="loading" class="text-center loader-block" style="display: none;">
        <div class="custom-loader"></div>
    </div>
    <script>
        // $(document).ready(async function() {
        //     const take = 10;
        //     let page = 1;
        //     let stopLoading = false;
        //     let searchQuery = null;
        //     let totalData = 0;
        //     let loadedDataCount = 0;

        //     async function loadData() {
        //         $('#loading').show();

        //         const requestData = {
        //             page: page,
        //             take: take,
        //         };

        //         if (searchQuery) {
        //             requestData.search = searchQuery;
        //         }

        //         console.log(requestData);

        //         try {
        //             const response = await $.ajax({
        //                 url: '/load-data',
        //                 method: 'GET',
        //                 data: requestData
        //             });
        //             $('#loading').hide();

        //             if (response.data.length > 0) {
        //                 response.data.forEach(function(item) {
        //                     const html = `
    //             <div class="card" style="width: 40rem">
    //                 <div class="card-header">
    //                     Universitas Muhammadiyah Purwokerto
    //                 </div>
    //                 <div class="card-body">
    //                     <h5 class="card-title">${item.judul}</h5>
    //                     <p class="card-text">${item.doi}</p>
    //                     <p class="card-text">${item.author}</p>
    //                 </div>
    //                 <div class="px-5 card-footer d-flex justify-content-end gap-3">
    //                     <a href="${item.web_link}" class="btn btn-primary">Lihat Detail</a>
    //                     <a href="${item.pdf_link}" class="btn btn-primary">Lihat Pdf</a>
    //                 </div>
    //             </div>
    //             `;
        //                     $('#data-container').append(html);
        //                 });

        //                 page++;
        //                 loadedDataCount += response.data.length;

        //                 totalData = response.total;

        //                 if (loadedDataCount >= totalData) {
        //                     stopLoading = true;
        //                 }
        //             }
        //         } catch (error) {
        //             $('#loading').hide();
        //             console.error(error);
        //             alert('Terjadi kesalahan saat memuat data.');
        //         }
        //     }

        //     function loadSearchData() {
        //         page = 1;
        //         loadedDataCount = 0;
        //         stopLoading = false;
        //         $('#data-container').empty(); // Clear the data container before loading new data
        //         loadData();
        //     }

        //     $('#search').on('input', function() {
        //         searchQuery = $(this).val();
        //         loadSearchData();
        //     });

        //     $(window).scroll(function() {
        //         if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
        //             if ($('#loading').is(':visible') || stopLoading) {
        //                 return;
        //             }
        //             loadData();
        //         }
        //     });

        //     // Initial load only page 1
        //     loadData();
        // });

        //FIX BISA
        $(document).ready(async function() {
            const take = 10;
            let page = 1;
            let stopLoading = false;
            let searchQuery = null;
            let totalData = 0;
            let loadedDataCount = 0;

            async function loadData() {
                $('#loading').show();

                const requestData = {
                    page: page,
                    take: take,
                };

                if (searchQuery) {
                    requestData.search = searchQuery;
                }

                console.log(requestData);

                try {
                    const response = await $.ajax({
                        url: '/load-data',
                        method: 'GET',
                        data: requestData
                    });
                    $('#loading').hide();

                    if (response.data.length > 0) {
                        response.data.forEach(function(item) {
                            const html = `
                                <div class="card" style="width: 40rem">
                                    <div class="card-header">
                                        Universitas Muhammadiyah Purwokerto
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">${item.judul}</h5>
                                        <p class="card-text">${item.doi}</p>
                                        <p class="card-text">${item.author}</p>
                                    </div>
                                    <div class="px-5 card-footer d-flex justify-content-end gap-3">
                                        <a href="${item.web_link}" class="btn-jurnal">Lihat Detail</a>
                                        <a href="${item.pdf_link}" class="btn-jurnal">Lihat Pdf</a>
                                    </div>
                                </div>
                                `;
                            $('#data-container').append(html);
                        });

                        page++;
                        loadedDataCount += response.data.length;

                        totalData = response.total;

                        if (loadedDataCount >= totalData) {
                            stopLoading = true;
                            const html = `
                        <div class="w-100 text-center">
                            <p>Tidak ada data lebih lanjut.</p>
                        </div>
                        `;
                            $('#data-container').append(html);
                        }
                    }
                } catch (error) {
                    $('#loading').hide();
                    console.error(error);
                    alert('Terjadi kesalahan saat memuat data.');
                }
            }

            function loadSearchData() {
                page = 1;
                loadedDataCount = 0;
                stopLoading = false;
                $('#data-container').empty();
                loadData();
            }

            $('#search-form').submit(function(e) {
                e.preventDefault();
                searchQuery = $('#search').val();
                loadSearchData();
            });

            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    if ($('#loading').is(':visible') || stopLoading) {
                        return;
                    }
                    loadData();
                }
            });

            await loadData();
        });
    </script>
@endsection
