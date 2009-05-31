function nonblank(s)  {
        for (var i = 0;  i < s.length;  i++)  {
                var c = s.charAt(i);
                if  (c != ' '  &&  c != '\t'  &&  c != '\n')
                        return  true;
        }
        return  false;
}
