jQuery(document).ready(function ($) {
    function initSortableSections() {
        $("#sortable-sections").sortable({
            handle: ".dashicons-move",  // Allows you to drag the section by this handle
            items: ".sortable-section", // Make the sections draggable
            update: function () {
                $("#sortable-sections .sortable-section").each(function (sectionIndex) {
                    const currentSectionId = $(this).data('section-id');
                    $(this)
                        .find("input, .sortable-lessons input")
                        .each(function () {
                            const name = $(this).attr("name");
                            if (name) {
                                const newName = name.replace(/\[\d+\]/, "[" + currentSectionId + "]");
                                $(this).attr("name", newName);
                            }
                        });
                });
                // Update lesson order after sections are sorted
                updateLessonOrders();
            },
        });
    }

    // Initialize sortable for lessons inside each section
    function initSortableLessons() {
        $(".sortable-lessons").sortable({
            handle: ".dashicons-move",
            items: ".sortable-lesson",
            connectWith: ".sortable-lessons",
            update: function () {
                updateLessonOrders();
            },
        });
    }

    // Function to recalculate lesson orders after sorting
    function updateLessonOrders() {
        $(".sortable-section").each(function (sectionIndex) {
            const lessonsList = $(this).find(".sortable-lesson");
            let lessonOrder = 1; // Reset lesson order for this section

            const currentSection = $(this).data('section-id');
            lessonsList.each(function (lessonIndex) {
                $(this)
                    .find("input")
                    .each(function () {
                        const name = $(this).attr("name");
                        if (name) {
                            // Update the name attribute for lesson order
                            const newName = name.replace(/section\[(\d+)\]\[lessons\]\[(\d+)\]/, "section[" + currentSection + "]" + "[lessons][" + lessonOrder + "]");
                            $(this).attr("name", newName);
                        }
                    });
                lessonOrder++; // Increment lesson order
            });
        });

        // Update the form data with correct lesson orders
        updateFormData();
    }

    // Function to update the hidden form data after sorting and changing lesson orders
    function updateFormData() {
        const sectionsData = [];

        // Gather all the section data
        $("#sortable-sections .sortable-section").each(function () {
            const sectionId = $(this).data("section-id");
            const sectionName = $(this).find("input[name^='section[" + sectionId + "][section_name]']").val();
            const lessonElements = $(this).find(".sortable-lesson");
            let lessonOrder = 1; // Start the order at 1 for each section

            const lessonsData = [];

            lessonElements.each(function () {
                const lessonId = $(this).find("input[name^='section[" + sectionId + "][lessons]']").val();
                lessonsData.push({
                    lesson_id: lessonId,
                    section_id: sectionId,
                    lesson_order: lessonOrder,
                    lesson_name: $(this).find("input[name^='section[" + sectionId + "][lessons]']").val(),
                });
                lessonOrder++; // Increment lesson order
            });

            sectionsData.push({
                section_id: sectionId,
                section_name: sectionName,
                lessons: lessonsData,
            });
        });

        // Update hidden input field with serialized sections and lessons data
    }

    // Initialize sortable on page load for sections and lessons
    initSortableSections();
    initSortableLessons();

    // Add new section dynamically
    $(document).on("click", ".add-new-section-button", function () {
        const sectionId = "new_" + Date.now();
        const newSection = `
            <tr class="sortable-section" data-section-id="${sectionId}">
                <td class="column-section-name" data-colname="Section Name" style="padding-top: 16px; padding-bottom: 16px; padding-left: 24px;">
                    <div style="display: flex; flex-direction: row; align-items: center; gap: 8px">
                        <span class="dashicons dashicons-move"></span>
                        <input type="hidden" name="section[${sectionId}][section_id]" value="${sectionId}">
                        <input type="text" name="section[${sectionId}][section_name]" value="" placeholder="Enter section name">
                    </div>
                    <div style="height: 100%; display: flex; align-items: center; position:relative;">
                        <a href="javascript:void(0)" class="delete-section-button delete-button">Verwijder sectie</a>
                    </div>
                </td>
                <td class="column-lessons" data-colname="Lessons">
                    <ul class="lessons-list sortable-lessons" data-section-id="${sectionId}">
                        <li class="add-lesson">
                            <a href="javascript:void(0)" style="margin-top: 16px"
                               class="add-lesson-button" data-section-index="${sectionId}">
                               + Voeg Les
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>`;

        // Prepend new section to be above the 'Add New Section' button
        $("#sortable-sections tr:last").before(newSection);

        // Reinitialize sortable for sections and lessons after adding new section
        initSortableSections();
        initSortableLessons();

        // Recalculate lesson orders after adding a new section
        updateLessonOrders();
    });

    $(document).on("click", ".add-lesson-button", function () {
        const sectionIndex = $(this).data("section-index");
        const lessonList = $(this).closest(".sortable-lessons");

        const newLesson = `
            <li class="sortable-lesson">
                <span class="dashicons dashicons-move"></span>
                <input type="hidden" name="section[${sectionIndex}][lessons][new_${Date.now()}][lesson_id]" value="">
                <input type="text" name="section[${sectionIndex}][lessons][new_${Date.now()}][lesson_name]" value="" placeholder="Enter lesson name">
                <span style="font-size: 10px">(verborgen)</span>
            </li>`;
        lessonList.find(".add-lesson").before(newLesson);

        updateLessonOrders();

        lessonList.sortable('refresh');
    });

    $(document).on("click", ".delete-section-button", function () {
        $(this).closest(".sortable-section").remove();
        updateLessonOrders();
    });
});
