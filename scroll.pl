#!/usr/bin/perl
#############################################################################
# PixelOverlay-ScrollingText.pl - Scroll a text string across a matrix
#############################################################################
# Set our library path to find the FPP Perl modules
use lib "/opt/fpp/lib/perl/";

# Use the FPP Memory Map module to talk to the daemon
use FPP::MemoryMap;

#############################################################################
# Setup some variables (this is the part you want to edit for font, color, etc.)
my $name  = "RGBMatrix";    # Memory Mapped block name
my $color = "#FF0000";      # Text Color (also names like 'red', 'blue', etc.)
my $fill  = "#000000";      # Fill color (not used currently)
my $font  = "fixed";        # Font Name
my $size  = "16";           # Font size
my $pos   = "scroll";       # Position: 'scroll', 'center', 'x,y' (ie, '10,20')
my $dir   = "R2L";          # Scroll Direction: 'R2L', 'L2R', 'T2B', 'B2T'
my $pps   = 25;             # Pixels Per Second
my $msg   = "scroll";

#############################################################################
# Main part of program

# Instantiate a new instance of the MemoryMap interface
my $fppmm = new FPP::MemoryMap;

# Open the maps
$fppmm->OpenMaps();

# Get info about the block we are interested in
my $blk   = $fppmm->GetBlockInfo($name);

# Clear the block, probably not necessary
$fppmm->SetBlockColor($blk, 0, 0, 0);

# Enable the block (pass 2 for transparent mode, or 3 for transparent RGB)
$fppmm->SetBlockState($blk, 1);

# Scroll the message
$fppmm->TextMessage($blk, $msg, $color, $fill, $font, $size, $pos, $dir, $pps);

# Disable the block
$fppmm->SetBlockState($blk, 0);

# Close the maps (shouldn't make it here with the above "while (1)" loop)
$fppmm->CloseMaps();

# Exit cleanly (shouldn't make it here with the above "while (1)" loop)
exit(0);

#############################################################################
