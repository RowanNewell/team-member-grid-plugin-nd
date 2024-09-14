jQuery(document).ready(function($) {
    const teamMembers = $(".team-member-nd");

    teamMembers.each(function() {
        $(this).on("click", function() {
            const grid = $(this).closest(".team-grid-nd");

            const gridItems = Array.from(grid.children());
            const memberIndex = gridItems.indexOf(this);  // Define the memberIndex here
            const gridColumns = window.getComputedStyle(grid[0]).gridTemplateColumns.split(" ").length;

            // Determine the row number
            const rowStartIndex = Math.floor(memberIndex / gridColumns) * gridColumns;
            const rowEndIndex = Math.min(rowStartIndex + gridColumns, gridItems.length) - 1;

            // Find the last item in the row where the member was clicked
            const lastItemInRow = gridItems[rowEndIndex];

            // Check if there's an existing detail container open for this specific team member
            const existingDetailContainer = $(lastItemInRow).next(".team-member-detail-nd");
            const isSameMember = existingDetailContainer.length && existingDetailContainer.data("memberIndex") === memberIndex;

            if (isSameMember) {
                // If the same member's container is open, close it and stop further execution
                existingDetailContainer.slideUp(300, function() {
                    $(this).remove();
                });
                return;
            }

            // Close and remove any other existing detail containers across all rows
            let slideUpTime = 0;
            if ($(".team-member-detail-nd").length) {
                slideUpTime = 300; // Set the delay time to match the slide up duration
                $(".team-member-detail-nd").slideUp(slideUpTime, function() {
                    $(this).remove();
                });
            }

            // Get the necessary details from the clicked member
            const picture = $(this).find("img").attr("src") || ''; // Default to empty string if not available
            const name = $(this).find("h3").text() || 'Unnamed'; // Default to 'Unnamed' if name is not provided
            const title = $(this).find("h4").text() || ''; // Default to empty string if not available
            const description = $(this).attr("data-description") || ''; // Default to empty string if not available

            // Create a new detail container
            const detailContainer = $(`
                <div class="team-member-detail-nd" style="display: none;" data-member-index="${memberIndex}">
                    <div class="team-member-content-nd">
                        <div class="img-cont" style="background-image: url('${picture}');"></div>
                        <div class="text-content-nd">
                            <h3>${name}</h3>
                            <h4>${title}</h4>
                            <p>${description}</p>
                        </div>
                        <button class="close-btn-nd">
                            <img src="${teamMembersData.closeIcon}" alt="Close Icon" />
                        </button>
                    </div>
                </div>
            `);

            // Insert the detail container after the last item in the current row
            $(lastItemInRow).after(detailContainer);

            // Delay the slide down only if there was an existing container that was closed
            setTimeout(() => {
                detailContainer.slideDown(300);
            }, slideUpTime);

            // Add close functionality to the close button with a slide-up animation
            detailContainer.find(".close-btn-nd").on("click", function(e) {
                e.stopPropagation(); // Prevent triggering the click on team member
                detailContainer.slideUp(300, function() {
                    $(this).remove();
                });
            });
        });
    });
});