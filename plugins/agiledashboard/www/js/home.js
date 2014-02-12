/**
 * Copyright (c) Enalean, 2014. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

/*
 * Requires jQuery and d3.js
 */
(function($) {
    $(document).ready(function() {
        $(".data-burndown-json").each(function() {
            var json = JSON.parse($(this).html()),
                placeholder = $(this).attr("data-for"),
                burndown;

            burndown = new tuleap.agiledashboard.Burndown(d3, json, {
                width: 320,
                height: 140,
            });
            burndown.display(d3.select("#" + placeholder));
        });
    });
})(window.jQuery)