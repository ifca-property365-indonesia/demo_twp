$PBExportHeader$bw_print_labels.srw
forward
global type bw_print_labels from bw_print
end type
type p_1 from picture within bw_print_labels
end type
end forward

global type bw_print_labels from bw_print
p_1 p_1
end type
global bw_print_labels bw_print_labels

on bw_print_labels.create
int iCurrent
call bw_print::create
this.p_1=create p_1
iCurrent=UpperBound(this.Control)
this.Control[iCurrent+1]=p_1
end on

on bw_print_labels.destroy
call bw_print::destroy
if IsValid(MenuID) then destroy(MenuID)
destroy(this.p_1)
end on

type p_1 from picture within bw_print_labels
int X=2711
int Width=106
int Height=93
boolean BringToTop=true
string PictureName="envelope.bmp"
boolean Border=true
BorderStyle BorderStyle=StyleRaised!
boolean FocusRectangle=false
end type

on clicked;openwithparm ( w_label, idw_current )
end on

