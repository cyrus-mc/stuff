#include <curses.h>

WINDOW *mywindow;

int main(void) {
	char input[10];
	int maxy, maxx;
	initscr();cbreak();clear();refresh();

	getmaxyx(stdscr, maxy, maxx);
	mywindow = newwin(4, 4, 4, 4);
	wmove(mywindow, 0, 0);
	wprintw(mywindow, "Testing\n");
	/*box(mywindow, ACS_VLINE, ACS_HLINE );
	wmove(mywindow, 10, 1);
	printw("mactella> ");
	scanw("%s", input);
	mvwprintw(mywindow, 0, 3, "%s", input);*/
	refresh();
	getch();
	endwin();
	return 0;
}
