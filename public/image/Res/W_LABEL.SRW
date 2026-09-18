$PBExportHeader$w_label.srw
forward
global type w_label from Window
end type
type ddlb_1 from dropdownlistbox within w_label
end type
type p_2 from picture within w_label
end type
type p_1 from picture within w_label
end type
type cb_2 from commandbutton within w_label
end type
type cb_1 from commandbutton within w_label
end type
type sle_labels_between_rows from singlelineedit within w_label
end type
type st_11 from statictext within w_label
end type
type rb_t_to_bottom from radiobutton within w_label
end type
type rb_l_to_right from radiobutton within w_label
end type
type st_10 from statictext within w_label
end type
type st_9 from statictext within w_label
end type
type st_8 from statictext within w_label
end type
type st_7 from statictext within w_label
end type
type st_6 from statictext within w_label
end type
type sle_labels_between_cols from singlelineedit within w_label
end type
type sle_labels_right from singlelineedit within w_label
end type
type sle_labels_left from singlelineedit within w_label
end type
type sle_labels_bottom from singlelineedit within w_label
end type
type sle_labels_top from singlelineedit within w_label
end type
type rb_sheet from radiobutton within w_label
end type
type rb_continous from radiobutton within w_label
end type
type sle_labels_down from singlelineedit within w_label
end type
type sle_labels_across from singlelineedit within w_label
end type
type sle_labels_height from singlelineedit within w_label
end type
type sle_labels_width from singlelineedit within w_label
end type
type st_5 from statictext within w_label
end type
type st_4 from statictext within w_label
end type
type st_3 from statictext within w_label
end type
type st_2 from statictext within w_label
end type
type st_1 from statictext within w_label
end type
type gb_4 from groupbox within w_label
end type
type gb_3 from groupbox within w_label
end type
type gb_2 from groupbox within w_label
end type
type gb_1 from groupbox within w_label
end type
end forward

global type w_label from Window
int X=673
int Y=265
int Width=2094
int Height=1165
boolean TitleBar=true
string Title="Specify Label Specifications"
long BackColor=12632256
boolean ControlMenu=true
boolean MinBox=true
boolean MaxBox=true
boolean Resizable=true
ddlb_1 ddlb_1
p_2 p_2
p_1 p_1
cb_2 cb_2
cb_1 cb_1
sle_labels_between_rows sle_labels_between_rows
st_11 st_11
rb_t_to_bottom rb_t_to_bottom
rb_l_to_right rb_l_to_right
st_10 st_10
st_9 st_9
st_8 st_8
st_7 st_7
st_6 st_6
sle_labels_between_cols sle_labels_between_cols
sle_labels_right sle_labels_right
sle_labels_left sle_labels_left
sle_labels_bottom sle_labels_bottom
sle_labels_top sle_labels_top
rb_sheet rb_sheet
rb_continous rb_continous
sle_labels_down sle_labels_down
sle_labels_across sle_labels_across
sle_labels_height sle_labels_height
sle_labels_width sle_labels_width
st_5 st_5
st_4 st_4
st_3 st_3
st_2 st_2
st_1 st_1
gb_4 gb_4
gb_3 gb_3
gb_2 gb_2
gb_1 gb_1
end type
global w_label w_label

type variables
string is_line
datawindow idw_1
string is_units
string is_shape
string is_sheet
end variables

on open;int li_fnum
int li_bytes = 1
idw_1 = message.powerobjectparm

li_fnum = fileopen ("labels.txt")

if (li_fnum < 0) then return //ERROR
do while (li_bytes > 0)
   li_bytes = fileread(li_fnum, is_line)
   if (li_bytes > 0) THEN
     ddlb_1.additem(is_line)
   end if
loop

fileclose(li_fnum)

end on

on w_label.create
this.ddlb_1=create ddlb_1
this.p_2=create p_2
this.p_1=create p_1
this.cb_2=create cb_2
this.cb_1=create cb_1
this.sle_labels_between_rows=create sle_labels_between_rows
this.st_11=create st_11
this.rb_t_to_bottom=create rb_t_to_bottom
this.rb_l_to_right=create rb_l_to_right
this.st_10=create st_10
this.st_9=create st_9
this.st_8=create st_8
this.st_7=create st_7
this.st_6=create st_6
this.sle_labels_between_cols=create sle_labels_between_cols
this.sle_labels_right=create sle_labels_right
this.sle_labels_left=create sle_labels_left
this.sle_labels_bottom=create sle_labels_bottom
this.sle_labels_top=create sle_labels_top
this.rb_sheet=create rb_sheet
this.rb_continous=create rb_continous
this.sle_labels_down=create sle_labels_down
this.sle_labels_across=create sle_labels_across
this.sle_labels_height=create sle_labels_height
this.sle_labels_width=create sle_labels_width
this.st_5=create st_5
this.st_4=create st_4
this.st_3=create st_3
this.st_2=create st_2
this.st_1=create st_1
this.gb_4=create gb_4
this.gb_3=create gb_3
this.gb_2=create gb_2
this.gb_1=create gb_1
this.Control[]={ this.ddlb_1,&
this.p_2,&
this.p_1,&
this.cb_2,&
this.cb_1,&
this.sle_labels_between_rows,&
this.st_11,&
this.rb_t_to_bottom,&
this.rb_l_to_right,&
this.st_10,&
this.st_9,&
this.st_8,&
this.st_7,&
this.st_6,&
this.sle_labels_between_cols,&
this.sle_labels_right,&
this.sle_labels_left,&
this.sle_labels_bottom,&
this.sle_labels_top,&
this.rb_sheet,&
this.rb_continous,&
this.sle_labels_down,&
this.sle_labels_across,&
this.sle_labels_height,&
this.sle_labels_width,&
this.st_5,&
this.st_4,&
this.st_3,&
this.st_2,&
this.st_1,&
this.gb_4,&
this.gb_3,&
this.gb_2,&
this.gb_1}
end on

on w_label.destroy
destroy(this.ddlb_1)
destroy(this.p_2)
destroy(this.p_1)
destroy(this.cb_2)
destroy(this.cb_1)
destroy(this.sle_labels_between_rows)
destroy(this.st_11)
destroy(this.rb_t_to_bottom)
destroy(this.rb_l_to_right)
destroy(this.st_10)
destroy(this.st_9)
destroy(this.st_8)
destroy(this.st_7)
destroy(this.st_6)
destroy(this.sle_labels_between_cols)
destroy(this.sle_labels_right)
destroy(this.sle_labels_left)
destroy(this.sle_labels_bottom)
destroy(this.sle_labels_top)
destroy(this.rb_sheet)
destroy(this.rb_continous)
destroy(this.sle_labels_down)
destroy(this.sle_labels_across)
destroy(this.sle_labels_height)
destroy(this.sle_labels_width)
destroy(this.st_5)
destroy(this.st_4)
destroy(this.st_3)
destroy(this.st_2)
destroy(this.st_1)
destroy(this.gb_4)
destroy(this.gb_3)
destroy(this.gb_2)
destroy(this.gb_1)
end on

type ddlb_1 from dropdownlistbox within w_label
int X=503
int Y=41
int Width=1194
int Height=1001
int TabOrder=10
BorderStyle BorderStyle=StyleLowered!
boolean VScrollBar=true
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

on selectionchanged;sle_labels_width.text  = string(profileint("pblab040.ini", this.text, "width", 0) /1000 )
sle_labels_height.text = string(profileint("pblab040.ini", this.text, "height", 0) /1000)
sle_labels_across.text = string(profileint("pblab040.ini", this.text, "columns", 0)) 
sle_labels_down.text   = string(profileint("pblab040.ini", this.text, "rows", 0))
sle_labels_top.text    = string(profileint("pblab040.ini", this.text, "top_margin", 0) / 1000)
sle_labels_bottom.text = string(profileint("pblab040.ini", this.text, "bottom_margin", 0) / 1000)
sle_labels_left.text   = string(profileint("pblab040.ini", this.text, "left_margin", 0) / 1000)
sle_labels_right.text  = string(profileint("pblab040.ini", this.text, "right_margin", 0) / 1000)
sle_labels_between_cols.text = string(profileint("pblab040.ini", this.text, "columns.spacing", 0) / 1000)
sle_labels_between_rows.text = string(profileint("pblab040.ini", this.text, "rows.spacing", 0) / 1000)

is_units = string(profileint("pblab040.ini", this.text, "units", 0) / 1000)

is_shape = string(profileint("pblab040.ini", this.text, "shape", 0) / 1000)

is_sheet = string(profileint("pblab040.ini", "sheet", "is_line", 0) / 1000)
if (integer(is_sheet) = 0) then 
   rb_continous.checked = true
   rb_sheet.checked     = false
else
   rb_continous.checked = false
   rb_sheet.checked     = true
end if

// 
rb_l_to_right.checked = true
rb_t_to_bottom.checked = false  


end on

type p_2 from picture within w_label
int X=1299
int Y=841
int Width=275
int Height=189
string PictureName="t_to_b.bmp"
boolean FocusRectangle=false
end type

type p_1 from picture within w_label
int X=1267
int Y=601
int Width=321
int Height=185
string PictureName="l_to_r.bmp"
boolean FocusRectangle=false
end type

type cb_2 from commandbutton within w_label
int X=1729
int Y=161
int Width=316
int Height=93
int TabOrder=50
string Text="Cancel"
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

on clicked;close(parent)
end on

type cb_1 from commandbutton within w_label
int X=1729
int Y=37
int Width=316
int Height=93
int TabOrder=20
string Text="OK"
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

on clicked;idw_1.Modify("DataWindow.Label.Width           =" + string(integer(sle_labels_width.text)        * 1000 ))
idw_1.Modify("DataWindow.Label.Top_Margin      =" + string(integer(sle_labels_top.text)          * 1000 ))
idw_1.Modify("DataWindow.Label.Columns         =" + string(integer(sle_labels_across.text)))  
idw_1.Modify("DataWindow.Label.Rows            =" + string(integer(sle_labels_down.text)  )) 
idw_1.Modify("DataWindow.Label.Height          =" + string(integer(sle_labels_height.text)       * 1000 ))
idw_1.Modify("DataWindow.Label.Bottom_Margin   =" + string(integer(sle_labels_bottom.text)       * 1000 ))
idw_1.Modify("DataWindow.Label.Left_Margin     =" + string(integer(sle_labels_left.text)         * 1000 ))
idw_1.Modify("DataWindow.Label.Right_Margin    =" + string(integer(sle_labels_right.text)        * 1000 ))
idw_1.Modify("DataWindow.Label.Columns_Spacing =" + string(integer(sle_labels_between_cols.text) * 1000 ))
idw_1.Modify("DataWindow.Label.Rows_Spacing    =" + string(integer(sle_labels_between_rows.text) * 1000 ))

idw_1.Modify("DataWindow.Label.Name    =" + is_line)
idw_1.Modify("DataWindow.Label.Shape   =" + is_shape)

if (integer(is_sheet) = 0) then
   idw_1.Modify("DataWindow.Label.Sheet  = no")
else
   idw_1.Modify("DataWindow.Label.Sheet  = yes")
end if

close(parent)
end on

type sle_labels_between_rows from singlelineedit within w_label
int X=714
int Y=917
int Width=202
int Height=89
int TabOrder=160
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_11 from statictext within w_label
int X=179
int Y=933
int Width=526
int Height=73
boolean Enabled=false
string Text="Between Ro&ws       :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type rb_t_to_bottom from radiobutton within w_label
int X=1038
int Y=789
int Width=499
int Height=73
string Text="T&op To Bottom"
BorderStyle BorderStyle=StyleLowered!
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type rb_l_to_right from radiobutton within w_label
int X=1038
int Y=541
int Width=449
int Height=73
string Text="&Left To Right"
BorderStyle BorderStyle=StyleLowered!
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_10 from statictext within w_label
int X=179
int Y=821
int Width=526
int Height=73
boolean Enabled=false
string Text="B&etween Columns :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_9 from statictext within w_label
int X=453
int Y=705
int Width=252
int Height=73
boolean Enabled=false
string Text="&Right      :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_8 from statictext within w_label
int X=42
int Y=705
int Width=170
int Height=73
boolean Enabled=false
string Text="&Left    :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_7 from statictext within w_label
int X=453
int Y=589
int Width=252
int Height=73
boolean Enabled=false
string Text="&Bottom  :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_6 from statictext within w_label
int X=42
int Y=589
int Width=179
int Height=73
boolean Enabled=false
string Text="&Top     :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type sle_labels_between_cols from singlelineedit within w_label
int X=714
int Y=805
int Width=202
int Height=89
int TabOrder=150
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type sle_labels_right from singlelineedit within w_label
int X=714
int Y=693
int Width=202
int Height=89
int TabOrder=140
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type sle_labels_left from singlelineedit within w_label
int X=229
int Y=693
int Width=202
int Height=89
int TabOrder=130
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type sle_labels_bottom from singlelineedit within w_label
int X=714
int Y=581
int Width=202
int Height=89
int TabOrder=120
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type sle_labels_top from singlelineedit within w_label
int X=229
int Y=581
int Width=202
int Height=89
int TabOrder=110
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type rb_sheet from radiobutton within w_label
int X=1180
int Y=345
int Width=398
int Height=73
string Text="Sheet Feed"
BorderStyle BorderStyle=StyleLowered!
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type rb_continous from radiobutton within w_label
int X=1180
int Y=253
int Width=490
int Height=73
string Text="Continous Feed"
BorderStyle BorderStyle=StyleLowered!
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type sle_labels_down from singlelineedit within w_label
int X=874
int Y=353
int Width=202
int Height=81
int TabOrder=80
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type sle_labels_across from singlelineedit within w_label
int X=439
int Y=353
int Width=202
int Height=81
int TabOrder=70
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type sle_labels_height from singlelineedit within w_label
int X=874
int Y=253
int Width=202
int Height=81
int TabOrder=40
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type sle_labels_width from singlelineedit within w_label
int X=439
int Y=253
int Width=202
int Height=81
int TabOrder=170
boolean AutoHScroll=false
long TextColor=33554432
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_5 from statictext within w_label
int X=663
int Y=361
int Width=215
int Height=73
boolean Enabled=false
string Text="&Down   :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_4 from statictext within w_label
int X=33
int Y=361
int Width=458
int Height=73
boolean Enabled=false
string Text="Labels &Across :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_3 from statictext within w_label
int X=663
int Y=261
int Width=220
int Height=73
boolean Enabled=false
string Text="&Height  :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_2 from statictext within w_label
int X=33
int Y=261
int Width=453
int Height=73
boolean Enabled=false
string Text="&Width                 :"
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type st_1 from statictext within w_label
int X=5
int Y=57
int Width=494
int Height=73
boolean Enabled=false
string Text="Predefined Label :"
Alignment Alignment=Center!
boolean FocusRectangle=false
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type gb_4 from groupbox within w_label
int X=1006
int Y=485
int Width=695
int Height=561
int TabOrder=100
string Text="Arrange"
BorderStyle BorderStyle=StyleLowered!
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type gb_3 from groupbox within w_label
int X=10
int Y=485
int Width=938
int Height=561
int TabOrder=90
string Text="Margins"
BorderStyle BorderStyle=StyleLowered!
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type gb_2 from groupbox within w_label
int X=1143
int Y=173
int Width=554
int Height=289
int TabOrder=60
string Text="Paper"
BorderStyle BorderStyle=StyleLowered!
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

type gb_1 from groupbox within w_label
int X=10
int Y=173
int Width=1093
int Height=289
int TabOrder=30
string Text="Label"
BorderStyle BorderStyle=StyleLowered!
long TextColor=33554432
long BackColor=12632256
int TextSize=-9
int Weight=700
string FaceName="Arial"
FontFamily FontFamily=Swiss!
FontPitch FontPitch=Variable!
end type

