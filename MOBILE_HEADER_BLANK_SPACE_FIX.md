# Mobile header blank-space fix

Fixed the large empty vertical area on small screens in My Work, Operations Board (Job Board / Task Board), and Jobs.

Root cause: a tablet flex rule gave the page-title block `flex-basis: 260px`. When the header switched to a vertical flex direction on phones, that basis became 260px of height.

The mobile override now resets the title/action blocks to automatic height while keeping the existing responsive filter/card behavior intact.
