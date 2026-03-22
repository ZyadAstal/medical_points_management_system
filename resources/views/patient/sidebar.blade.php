<aside class="sidebar-new">
    <div class="sidebar-brand">
        <img class="sidebar-logo" src="{{ asset('assets/patient/logos/white-logo.svg') }}" alt="Medicare" />
    </div>

    <nav class="sidebar-nav" aria-label="القائمة الجانبية">
        <a class="sidebar-link nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}" 
           href="{{ route('patient.dashboard') }}" data-page="dashboard">
            <img class="sidebar-icon" src="{{ asset('assets/patient/icons/dashboard.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text" style="font-family: 'Tajawal', sans-serif;">لوحة التحكم</span>
        </a>

        <a class="sidebar-link nav-link {{ request()->routeIs('patient.medicines.search') ? 'active' : '' }}" 
           href="{{ route('patient.medicines.search') }}" data-page="medicine-search">
            <svg class="sidebar-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="24" height="24" fill="url(#pattern_med_search)"/>
                <defs>
                    <pattern id="pattern_med_search" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <use xlink:href="#image_med_search" transform="scale(0.0333333)"/>
                    </pattern>
                    <image id="image_med_search" width="30" height="30" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAACXBIWXMAAAsTAAALEwEAmpwYAAABdUlEQVR4nO2WwUpCQRiFL5lusnYJvYL1DtneRUb5CmJYWk8RvoZZjxJEmyxISdu31kWtvvjtXJiVMjMXCfLAgOCc882d+e8/N0nW+isCCkAduAOGwEzDfvf1XyFr6AkwYbnGQC0L4AbQdYKfgUugDGxp7ANtYODMuzFvDLiroC+gsShMi2xq7hwes70o6NDDV3HgxyGFNJG5EbDoc3nfgbyPse6cqfdZATngRRmnPsZ7mS58oU5GRxm3PqaRTOUIsFW7aehjmspUjABvK2O6avBOCHiUwVYfpJXtY+rL1I4AXyujF/I6DSJep1dlnPk2kLGMzQBwS94PYNPXXJPZ2l/Fw3cEfMtb9V30XLplUri1wVyyeHtbDvQT2E1CxO+Nk8JRG+yoORQ1rHqvnDNNoen8UhDcZLeMGv4y2ZlW7UmdXh0Nz1vDt94LvKnJzLSgnlWvW0gGywzuKz15+mVii91L/gu85Gz748rADvwJeFgpeK1E+gHiePXNoZbl0QAAAABJRU5ErkJggg=="/>
                </defs>
            </svg>
            <span class="sidebar-link-text" style="font-family: 'Tajawal', sans-serif;">استعلام عن الأدوية</span>
        </a>

        <a class="sidebar-link nav-link {{ request()->routeIs('patient.prescriptions.index') ? 'active' : '' }}" 
           href="{{ route('patient.prescriptions.index') }}" data-page="prescriptions">
            <svg class="sidebar-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="24" height="24" fill="url(#pattern_prescriptions)"/>
                <defs>
                    <pattern id="pattern_prescriptions" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <use xlink:href="#image_prescriptions" transform="scale(0.02)"/>
                    </pattern>
                    <image id="image_prescriptions" width="50" height="50" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAABqUlEQVR4nO2YO04DMRCGnTRpoASFA6RCnAFKEg6Qe1CSO9BxCArEXdhI6YEaaJBQJPQho4kYKfv2YzfBv7TKatfz+GJ77LUxSTskwmoFnOwDSDwYRKH8RoMhPEimfo99x9kKGMovcAQ8Be8ZAoPIfXgYIoBEgSESSHAYIoIEhSEySDCYLkCCwHQF4h0mwjrSRJlzwL7s4YxrQK8UXeRBAvErEogogXgWCaQDEOAKeAVegGlnIGWJ1LS3dhs9t83DB0hhIjVsB8B75yD24ABYq0Q+gVGDOAsNAVy2yaMsQKUDgVhKUwvzLff3wLBGjJnY2GvWNg8nENlqZ2qrPQZOgTd5dlfhf6KG1E3bPJxAcr4XxurdOfAl764L7A/Un/Bo54mJCaKq02ZOLPMO14C5GjLznMn9oOwPm+bhA0RXp3XZCaHtDWlne+ciZ3LbYTVpk4dvkMoyC9xK2w/grM7kjgUyFZitMlngYygVDFXRrBYueTSSs4M/PyNZW1BDarBzIB5W/l81sfHrwGFI9hbERSQQUQLxLBKIKIF4FglkX0H6IvPvQZJMfP0AsGiAOwQlgwIAAAAASUVORK5CYII="/>
                </defs>
            </svg>
            <span class="sidebar-link-text" style="font-family: 'Tajawal', sans-serif;">وصفاتي الطبية</span>
        </a>

        <a class="sidebar-link nav-link {{ request()->routeIs('patient.dispense.index') ? 'active' : '' }}" 
           href="{{ route('patient.dispense.index') }}" data-page="dispenses">
            <svg class="sidebar-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="24" height="24" fill="url(#pattern_dispenses)"/>
                <defs>
                    <pattern id="pattern_dispenses" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <use xlink:href="#image_dispenses" transform="scale(0.02)"/>
                    </pattern>
                    <image id="image_dispenses" width="50" height="50" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADiElEQVR4nO2aSWgUQRSGKwkGYwwukOglFzfEg4rkKkFcosa4QCR6EXLQi0c9uYAaJeBCXBBBRPQmXiOoBw0YF0QUNTExIFEPCkJEhaAxip885hUp2xlnpru6MwP+MAwUXe+9j3rVVfWqjfmvaAIWAXuB28BLYFh/A0AXcACoA0pMIQpYBzwldw0AW4BSUwgCaoE7ToDvgHNAIzAXqNTfPGA1cBp44zz/BJg/3hD1wAcN6D2wEyjPoV8Z0Aq81b5fgA3JRP13MOuBUQ3kOjAthI1K4Ira+AW0xBPtvyG+awAnouQ5UALsV1tf5UXgN9rcINo92j2vNmX+VPiymyiECJigE1+02xQjhBWwSu1/BKpMMUJYAffVT7PxKWANMKLGj3k1nkbALvV1yRTjSFgBC9TfoCnGkbCSBVXXlJHI+7HASJyMZCyc/yH1PT2KkSYHQvQcqPYaaRYBn9R3VZQdrIU4qxCinqRggAr1ORzWQKMzJzq0rToAU+M78KCAJeqv30SFAO4B3Q5MT1IwjO27zuTbcW2akeiWM4bzTE0SMKQ2kM/UT0PYV2xHlmddmN44YIBmZ+NYnmsnOa19S5dOgefcNIsNhtTZRM74otZQEOnSyXk2XZr1OjAzPKXUVbUp5/6yUBAhHLswL6LCAG3Ornd2Lh1W5ptOmdoleIWIBEOqPCT6AazItd4k9SXRqXzSKVN7VBj+hMi+bde14LV2uuCzOBYWhnwhtNNN7XQDeBg2nTK1B2D6ssEQEqJBO0n9aWrUdMrUrjB9DsxMbxAi4JF23G5iVjYYIkAsZEydPtMpU7sEnw6GsBDaeY9TkvSeTpnaAzD9wPHQEGpQyviiTSZhKYxAWIWDUGOv1MicONMpU3vkkbByFsCJcadTsD3SnEizEbPH1ikmQQEHvUBYOfcPjxNMp0NeIUTAAzX6OaF0avMOIQKOquE2E7OckfgJbPVtfKlz8VgaYzodjmUkAnd1dte7MaZ0OhIrhBWwTR0JUKXxKGdO+E+nDKNi778vumeRiOnUnshIuNL7bjkPowGUREinu3oBmiyEFbDcuU6+DEw2eUrPM9fUxmjiEFbAMqdkL3OmxZZesqRTJ7DD+WhA/uvNeAqYpcddq0Fdb4b0VnWSVsRrtSYsi6kreVPVmkIQqX3YZqeyl4ukKt9kClGkgOp0p9oV+CxJzhK3gH3A4vGO1RS7fgMS0AsB07g0oQAAAABJRU5ErkJggg=="/>
                </defs>
            </svg>
            <span class="sidebar-link-text" style="font-family: 'Tajawal', sans-serif;">الأدوية المصروفة</span>
        </a>

        <a class="sidebar-link nav-link {{ request()->routeIs('patient.profile') ? 'active' : '' }}" 
           href="{{ route('patient.profile') }}" data-page="profile">
            <img class="sidebar-icon" src="{{ asset('assets/patient/icons/profile.svg') }}" alt="" aria-hidden="true" />
            <span class="sidebar-link-text" style="font-family: 'Tajawal', sans-serif;">الملف الشخصي</span>
        </a>
    </nav>
</aside>
