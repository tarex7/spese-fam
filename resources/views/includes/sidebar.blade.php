<!-- Menu Laterale -->
<nav class="col-1 pl-1 bg-primary sidebar" style="height: 100vh;min-width:200px">
    <div class="position-sticky ">
        <ul class="nav flex-column py-4 mt-5 ">
            <li class="nav-item ">
                <a class="nav-link active text-white d-flex align-items-center" href="{{route('entrate')}}">
                    <i class="fa-solid fa-circle-dollar-to-slot mr-2"></i>
                    <p class="m-0"> Entrate</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-white d-flex align-items-center" href="{{route('spese')}}">
                    <i class="fa-solid fa-hand-holding-dollar mr-2"></i>
                    <p class="m-0"> Spese</p>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link active text-white d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#categorieDropdown">
                    <i class="fa-solid fa-list mr-2"></i> <p class="m-0"> Categorie</p>
                </a>
                <div class="collapse" id="categorieDropdown">
                    <ul class="nav flex-column pl-4">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="{{route('categorie')}}">
                                <i class="fa-solid fa-circle fa-2xs mr-1"></i> Spese
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="{{route('categorie_entrate')}}">
                                <i class="fa-solid fa-circle fa-2xs mr-1"></i> Entrate
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link active text-white d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#importaDropdown">
                    <i class="fa-solid fa-list mr-2"></i> <p class="m-0"> Importa</p>
                </a>
                <div class="collapse" id="importaDropdown">
                    <ul class="nav flex-column pl-4">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="{{route('spese/importa')}}">
                                <i class="fa-solid fa-circle fa-2xs mr-1"></i> Spese
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="{{route('entrate/importa')}}">
                                <i class="fa-solid fa-circle fa-2xs mr-1"></i> Entrate
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

<!--            <li class="nav-item">
                <a class="nav-link text-white" href="{{route('spese/importa')}}">
                    <i class="fa-solid fa-file-import mr-2"></i> Importa
                </a>
            </li>-->
            <!-- Aggiungi altre voci di menu secondo le tue esigenze -->
        </ul>
    </div>
</nav>






